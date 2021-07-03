<?php

namespace App\Controller;

use App\Entity\Condidat;
use App\Entity\Note;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\User;
/**
 * @Route("/admin")
 */

class CandidatureController extends AbstractController
{

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
     * @Route("/candidatures/{id}/info", name="info_candidature")
     */
    public function info(Condidat $id, EntityManagerInterface $manager)
    {

        $note=$manager->getRepository(Note::class)->findOneBy(['condidat'=>$id]);
        $reponses=$manager->getRepository(Reponse::class)->findBy(['condidate'=>$id]);
        $reponses_candidat=[];
        foreach ($reponses as $reponse) {
            $reponses_candidat[]=$reponse->getOptionQuestion()->getId();
        }

        return $this->render('candidature/info.html.twig', [
            'controller_name' => 'SecurityController',
            'candidat'=>$id,
            'note'=>$note,
            'reponses_candidat'=>$reponses_candidat,
            'title'=>'Reponses'
        ]);

    }

    /**
     * @Route("/candidatures", name="candidatures")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory)
    {


        $reponses = $this->getDoctrine()
            ->getRepository(Condidat::class)
            ->findBy(['correcteur'=>$this->getUser()], array('id' => 'DESC') );
        $tab=[];
        foreach ($reponses as $reponse){
            $tab2=[];
            $tab2['id'] =  $reponse->getId();

            $tab2['candidat'] = $reponse;
            $tab2['quiz'] = $reponse->getQuiz();
            $tab2['correcteur'] = $reponse->getCorrecteur();

            $tab2['actions']='<div style="float: right">
<a href="'.$this->generateUrl("info_candidature",['id'=> $reponse->getId()]).'">\'<i class="fas fa-eye text-info ml-2 " style="cursor: pointer" onclick="add_correcteur('.$reponse->getId().')">
</i>
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
        return $this->render('candidature/index.html.twig', [
            'controller_name' => 'SecurityController',
            'datatable' => $table,
            'title'=>'Reponses'
        ]);
    }
}

