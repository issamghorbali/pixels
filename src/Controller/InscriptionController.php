<?php

namespace App\Controller;

use App\Entity\Condidat;
use App\Entity\OptionQuestion;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\CondidatType;
use App\Form\OptionType;
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

class InscriptionController extends AbstractController
{
    /**
     * @Route("/quiz/{id}/candidat/save", name="save_candidat", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_candidat(Quiz $id,Request $request, ValidatorInterface $validator,EntityManagerInterface $manager)
    {

        //if($request->isXmlHttpRequest())
        {
            $form =  $this->createForm(CondidatType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }



            $candidat = new Condidat();
            $candidat->setNom($request->request->get('nom'));
            $candidat->setPrenom($request->request->get('prenom'));
            $candidat->setAnneeExperience($request->request->get('annee_experience'));
            $candidat->setLangage($request->request->get('langage'));
            $candidat->setEmail($request->request->get('email'));
            $candidat->setQuiz($id);


            $manager->persist($candidat);
            $manager->flush();

            $b=array(
                'result' =>1,
                'id'=>$candidat->getId(),
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




}

