<?php

namespace App\Controller;

use App\Entity\Condidat;
use App\Entity\Quiz;
use App\Entity\Reponse;
use App\Form\CondidatType;
use App\Form\CorrecteurType;
use App\Form\QuizType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\User;
/**
 * @Route("/admin")
 */

class ReponsesController extends AbstractController
{
    /**
     * @Route("/reponses/{id}/correcteur/save", name="save_correcteur", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_correcteur(Condidat $id, Request $request, ValidatorInterface $validator,EntityManagerInterface $manager,MailerInterface $mailer)
    {

        //if($request->isXmlHttpRequest())
        {
            $form =  $this->createForm(CorrecteurType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }



            $candidat = $id;


            $candidat->setCorrecteur($manager->getRepository(\App\Entity\User::class)->find($request->request->get('correcteur')));
            $manager->persist($candidat);
            $manager->flush();


            $correcteur=$manager->getRepository(\App\Entity\User::class)->find($request->request->get('correcteur'));
            $email = (new TemplatedEmail())
                ->from(new Address('issam.ghorbali@gmail.com', 'QUIZZ'))
                ->to($correcteur->getEmail())
                //->cc('issam.ghorbali@gmail.com')
                ->subject('Candidature QUIZZ')
                ->htmlTemplate('reponses/email_notif_correcteur.html.twig')
                ->context([
                    'user' =>$correcteur,
                    'quiz'=>$id->getQuiz(),
                    'condidat'=>$id,
                    'tokenLifetime' =>''
                ])
            ;

            $mailer->send($email);

            $b=array(
                'result' =>1,
                'message' => 'success',
            );
            return new JsonResponse($b);
        }


    }

    /**
     * @Route("/reponse/delete", name="delete_reponse", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_reponse(Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {


        $quiz = $this->getDoctrine()->getRepository(Condidat::class)->find($request->request->get('id'));
        $manager->remove($quiz);
        $manager->flush();

        $b=array(
            'result' =>1,
            'message' => 'success',
        );
        return new JsonResponse($b);



    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    /**
     * @Route("/reponses/{id}/correcteur/add", name="add_correcteur")
     */
    public function add_correcteur(Condidat $id)
    {
        $form =  $this->createForm(CorrecteurType::class, $id);

        return $this->render('reponses/add_correcteur.html.twig', [
            'form' => $form->createView(),
            'candidat'=>$id
        ]);
    }



    /**
     * @Route("/reponses", name="reponses")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory)
    {


        $reponses = $this->getDoctrine()
            ->getRepository(Condidat::class)
            ->findBy(array(), array('id' => 'DESC') );
        $tab=[];
        foreach ($reponses as $reponse){
            $tab2=[];
            $tab2['id'] =  $reponse->getId();

            $tab2['candidat'] = $reponse;
            $tab2['quiz'] = $reponse->getQuiz();
            $tab2['correcteur'] = $reponse->getCorrecteur();

               $tab2['actions']='<div style="float: right">
<i class="fas fa-edit text-info ml-2 " style="cursor: pointer" onclick="add_correcteur('.$reponse->getId().')">
</i>
<a href="'.$this->generateUrl('info_candidature', ['id'=>$reponse->getId()]).'">
<i class="fas fa-eye text-info ml-2 " style="cursor: pointer" ></i>
</i>
<i class="fas fa-trash-alt text-danger ml-2 "  style="cursor: pointer"  onclick="delete_reponse('.$reponse->getId().')"></i>
</div>';
            $tab[]=$tab2;
        }
        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => 'ID','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])


            ->add('candidat', TextColumn::class, ['label' => 'Candidat','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])
            ->add('quiz', TextColumn::class, ['label' => 'Quiz','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])
            ->add('correcteur', TextColumn::class, ['label' => 'Correcteur','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])
            ->add('actions', TextColumn::class, ['label' => 'Actions', 'render' => '%s', 'raw' => true,'orderable' => false,'searchable' => false])
            ->createAdapter(ArrayAdapter::class, $tab)
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('reponses/index.html.twig', [
            'controller_name' => 'SecurityController',
            'datatable' => $table,
            'title'=>'Reponses'
        ]);
    }
}

