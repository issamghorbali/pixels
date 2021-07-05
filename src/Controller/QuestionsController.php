<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\QuestionType;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * @Route("/admin")
 */
class QuestionsController extends AbstractController
{
    /**
     * @Route("/quiz/{id}/question/save", name="save_question", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_user(Quiz $id,Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {

        //if($request->isXmlHttpRequest())
        {
            $form =  $this->createForm(QuestionType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }



            $question = new Question();
            $question->setTitle($request->request->get('title'));
            $question->setQuiz($id);
            $manager->persist($question);
            $manager->flush();

            $b=array(
                'result' =>1,
                'message' => 'success',
            );
            return new JsonResponse($b);
        }


    }
    /**
     * @Route("/question/{id}/update", name="update_question", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function update_question($id,Request $request, ValidatorInterface $validator, EntityManagerInterface $manager)
    {
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);

        if($request->isXmlHttpRequest()) {
            $form =  $this->createForm(QuestionType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }
            $question->setTitle($request->request->get('title'));

            $manager->persist($question);
            $manager->flush();


            $b=array(
                'result' =>1,
                'message' => 'success',
            );
            return new JsonResponse($b);
        }


    }
    /**
     * @Route("/question/delete", name="delete_question", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_question(Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {


        $question = $this->getDoctrine()->getRepository(Question::class)->find($request->request->get('id'));
        $manager->remove($question);
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
     * @Route("/quizs/{id}/question/add", name="add_question")
     * @ParamConverter("quiz", options={"mapping"={"id"="quiz"}})
     */
    public function add_question(Quiz $id)
    {

        $form =  $this->createForm(QuestionType::class);

        return $this->render('questions/add_question.html.twig', [
            'form' => $form->createView(),
            'quiz'=>$id
        ]);
    }
    /**
     * @Route("/question/edit/{id}", name="edit_question")
     */
    public function edit_question($id)
    {
        $question= $this->getDoctrine()->getRepository(Question::class)->find($id);


        $form =  $this->createForm(QuestionType::class, $question);

        return $this->render('questions/edit_question.html.twig', [
            'form' => $form->createView(),
            'question'=>$question
        ]);
    }


    /**
     * @Route("/quizs/{id}/questions", name="question")
     */
    public function index(Quiz $id, Request $request, DataTableFactory $dataTableFactory)
    {


        $questions = $this->getDoctrine()
            ->getRepository(Question::class)
            ->findBy(['quiz'=>$id], array('id' => 'DESC') );

        return $this->render('questions/index.html.twig', [
            'controller_name' => 'SecurityController',
            'quiz' => $id,
            'questions'=>$questions,
            'title'=>'Questinss'
        ]);
    }
}

