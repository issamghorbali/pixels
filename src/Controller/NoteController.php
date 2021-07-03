<?php

namespace App\Controller;

use App\Entity\Condidat;
use App\Entity\Note;
use App\Entity\OptionQuestion;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\NoteType;
use App\Form\OptionType;
use App\Form\QuestionType;
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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mime\Address;
/**
 * @Route("/admin")
 */
class NoteController extends AbstractController
{
    /**
     * @Route("/candidature/{id}/note/save", name="save_note", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_note(Condidat $id,Request $request, ValidatorInterface $validator,EntityManagerInterface $manager,MailerInterface $mailer)
    {
        //if($request->isXmlHttpRequest())
        {
            $form =  $this->createForm(NoteType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }



            $note = new Note();
            $note->setNote($request->request->get('note'));
            $note->setDescription($request->request->get('description'));
            $note->setCondidat($id);
            $manager->persist($note);
            $manager->flush();


            $email = (new TemplatedEmail())
                ->from(new Address('issam.ghorbali@gmail.com', 'QUIZZ'))
                ->to($id->getEmail())
                //->cc('issam.ghorbali@gmail.com')
                ->subject('Note QUIZZ')
                ->htmlTemplate('reponses/email_candidat.html.twig')
                ->context([
                    'user' =>$id,
                    'note'=>$note,
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
     * @Route("/candidature/{id}/note/add", name="add_note")
     */
    public function add_note(Condidat $id)
    {

        $form =  $this->createForm(NoteType::class);

        return $this->render('candidature/add_note.html.twig', [
            'form' => $form->createView(),
            'candidat'=>$id
        ]);
    }



}

