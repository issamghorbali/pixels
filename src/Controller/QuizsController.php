<?php

namespace App\Controller;

use App\Entity\Quiz;
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

/**
 * @Route("/admin")
 */
class QuizsController extends AbstractController
{

public function  __construct()
{
}

    /**
     * @Route("/quiz/save", name="save_quiz", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_user(Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {

        //if($request->isXmlHttpRequest())
        {
            $form =  $this->createForm(QuizType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }



            $user = new Quiz();
            $user->setTitle($request->request->get('title'));
            $user->setDuree($request->request->get('duree'));
            $manager->persist($user);
            $manager->flush();

            $b=array(
                'result' =>1,
                'message' => 'success',
            );
            return new JsonResponse($b);
        }


    }
    /**
     * @Route("/quiz/{id}/update", name="update_quiz", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function update_user($id,Request $request, ValidatorInterface $validator, EntityManagerInterface $manager)
    {
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->find($id);

        if($request->isXmlHttpRequest()) {
            $form =  $this->createForm(QuizType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }
            $quiz->setTitle($request->request->get('title'));
            $quiz->setDuree($request->request->get('duree'));

            $manager->persist($quiz);
            $manager->flush();


            $b=array(
                'result' =>1,
                'message' => 'success',
            );
            return new JsonResponse($b);
        }


    }
    /**
     * @Route("/quiz/delete", name="delete_quiz", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_user(Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {


        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->find($request->request->get('id'));
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
     * @Route("/quizs/add", name="add_quiz")
     */
    public function add_quiz()
    {
        $task = new Quiz();

        $form =  $this->createForm(QuizType::class);

        return $this->render('quizs/add_quiz.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/quizs/edit/{id}", name="edit_quiz")
     */
    public function edit_quiz($id)
    {
        $quiz = $this->getDoctrine()->getRepository(Quiz::class)->find($id);


        $form =  $this->createForm(QuizType::class, $quiz);

        return $this->render('quizs/edit_quiz.html.twig', [
            'form' => $form->createView(),
            'quiz'=>$quiz
        ]);
    }


    /**
     * @Route("/quizs", name="quizs")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory)
    {


        $quizs = $this->getDoctrine()
            ->getRepository(Quiz::class)
            ->findBy(array(), array('id' => 'DESC') );
        $tab=[];
        foreach ($quizs as $quiz){
            $tab2=[];
            $tab2['id'] =  $quiz->getId();

            $tab2['title'] = $quiz->getTitle();

            $tab2['duree'] = $quiz->getDuree();
            $tab2['questions'] = '<a href="'.$this->generateUrl('question', ['id'=>$quiz->getId()]).'">'.count($quiz-> getQuestions()).'</a>';
            $tab2['actions']='<div style="float: right">
<i class="fas fa-edit text-info ml-2 " style="cursor: pointer" onclick="edit_quiz('.$quiz->getId().')">
</i>
<i class="fas fa-trash-alt text-danger ml-2 "  style="cursor: pointer"  onclick="delete_quiz('.$quiz->getId().')"></i>
</div>';
            $tab[]=$tab2;
        }
        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => 'ID','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])


            ->add('title', TextColumn::class, ['label' => 'Title','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])
            ->add('duree', TextColumn::class, ['label' => 'Duree','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])
            ->add('questions', TextColumn::class, ['label' => 'Questions','render'=>'%s', 'raw' => true, 'className' => 'bold','orderable' => true,'searchable' => true])
            ->add('actions', TextColumn::class, ['label' => 'Actions', 'render' => '%s', 'raw' => true,'orderable' => false,'searchable' => false])
            ->createAdapter(ArrayAdapter::class, $tab)
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('quizs/index.html.twig', [
            'controller_name' => 'SecurityController',
            'datatable' => $table,
            'title'=>'Quizs'
        ]);
    }
}

