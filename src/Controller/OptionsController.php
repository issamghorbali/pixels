<?php

namespace App\Controller;

use App\Entity\OptionQuestion;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\OptionType;
use App\Form\QuestionType;
use App\Form\QuizType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
/**
 * @Route("/admin")
 */
class OptionsController extends AbstractController
{
    /**
     * @Route("/admin")
     * /
    /**
     * @Route("/question/{id}/option/save", name="save_option", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_option(Question $id,Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {

        //if($request->isXmlHttpRequest())
        {
            $form =  $this->createForm(OptionType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }



            $question = new OptionQuestion();
            $question->setTitle($request->request->get('title'));
            $question->setQuestion($id);
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
     * @Route("/option/{id}/update", name="update_option", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function update_option($id,Request $request, ValidatorInterface $validator, EntityManagerInterface $manager)
    {
        $option = $this->getDoctrine()->getRepository(OptionQuestion::class)->find($id);

        if($request->isXmlHttpRequest()) {
            $form =  $this->createForm(OptionType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }
            $option->setTitle($request->request->get('title'));

            $manager->persist($option);
            $manager->flush();


            $b=array(
                'result' =>1,
                'message' => 'success',
            );
            return new JsonResponse($b);
        }


    }
    /**
     * @Route("/option/delete", name="delete_option", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_option(Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {


        $question = $this->getDoctrine()->getRepository(OptionQuestion::class)->find($request->request->get('id'));
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
     * @Route("/question/{id}/option/add", name="add_option")
     */
    public function add_question(Question $id)
    {

        $form =  $this->createForm(OptionType::class);

        return $this->render('options/add_option.html.twig', [
            'form' => $form->createView(),
            'question'=>$id
        ]);
    }
    /**
     * @Route("/option/edit/{id}", name="edit_option")
     */
    public function edit_option($id)
    {
        $option= $this->getDoctrine()->getRepository(OptionQuestion::class)->find($id);


        $form =  $this->createForm(QuestionType::class, $option);

        return $this->render('options/edit_option.html.twig', [
            'form' => $form->createView(),
            'question'=>$option
        ]);
    }



}

