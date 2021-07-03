<?php

namespace App\Controller;

use App\Entity\Condidat;
use App\Entity\Note;
use App\Entity\OptionQuestion;
use App\Entity\Quiz;
use App\Entity\Reponse;
use App\Form\CondidatType;
use App\Form\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FrontController extends AbstractController
{
    /**
     * @Route("/front", name="front")
     */
    public function index(EntityManagerInterface $manager): Response
    {
        $quizs=$manager->getRepository(Quiz::class)->findAll();
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'quizs'=>$quizs
        ]);
    }

    /**
     * @Route("/quiz/{id}/inscription", name="inscription")
     */
    public function inscription(Quiz $id, EntityManagerInterface $manager): Response
    {
        $form =  $this->createForm(CondidatType::class);


        $quizs=$manager->getRepository(Quiz::class)->findAll();
        return $this->render('front/inscription.html.twig', [
            'controller_name' => 'FrontController',
            'form' => $form->createView(),
            'quiz'=>$id
        ]);
    }

    /**
     * @Route("/quiz/{quiz}/{candidat}/test", name="test")
     */
    public function test(Quiz $quiz, Condidat $candidat, EntityManagerInterface $manager): Response
    {
        $form =  $this->createForm(CondidatType::class);


        $quizs=$manager->getRepository(Quiz::class)->findAll();
        return $this->render('front/test.html.twig', [
            'controller_name' => 'FrontController',
            'form' => $form->createView(),
            'quiz'=>$quiz,
            'candidat'=>$candidat
        ]);
    }


    /**
     * @Route("/quiz/{quiz}/{candidat}/test/save", name="save_test", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_test(Quiz $quiz, Condidat $condidat,Request $request,EntityManagerInterface $manager)
    {

$options=$request->request->all();
foreach ($options as $option){
    $reponse=new Reponse();
    $reponse->setCondidate($condidat);
    $reponse->setOptionQuestion($manager->getRepository(OptionQuestion::class)->find($option));
    $manager->persist($reponse);
}
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

}
