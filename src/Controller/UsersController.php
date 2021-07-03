<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Omines\DataTablesBundle\Adapter\ArrayAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mime\Address;
/**
 * @Route("/admin")
 */
class UsersController extends AbstractController
{

    private $passwordEncoder;
    private $params;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ParameterBagInterface $params) {
        $this->passwordEncoder = $passwordEncoder;
        $this->params = $params;
    }


    /**
     * @Route("/user/save", name="save_user", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function save_user(Request $request, ValidatorInterface $validator,EntityManagerInterface $manager,MailerInterface $mailer)
    {

        //if($request->isXmlHttpRequest())
        {
            $form =  $this->createForm(UserType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }

            $fileName='';
            $file = $request->files->get ( 'picture' );
            if($file){
                $fileName = md5 ( uniqid () ) . '.' . $file->guessExtension ();
                $original_name = $file->getClientOriginalName ();
                $file->move ( $this->getParameter ( 'users_directory' ), $fileName );
                //$imageFile = $form->getData('picture');;
                //dd($imageFile);
            }


            $user = new User();
            // ...

            $user->setFirstName($request->request->get('first_name'));
            $user->setLastName($request->request->get('last_name'));
            $user->setemail($request->request->get('email'));
            $user->setRoles([$request->request->get('roles')]);

            $user->setPicture( $fileName);

            $user->setPassword($this->passwordEncoder->encodePassword( $user,$request->request->get('password')['first']));

            $manager->persist($user);
            $manager->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('issam.ghorbali@gmail.com', 'QUIZZ'))
                ->to($user->getEmail())
                //->cc('issam.ghorbali@gmail.com')
                ->subject('Inscription QUIZZ')
                ->htmlTemplate('users/new_user_email.html.twig')
                ->context([
                    'user' =>$user,
                    'password'=>$request->request->get('password')['first'],
                    'tokenLifetime' =>''
                ])
            ;

            $mailer->send($email);


            //  $res= $bus->dispatch(new SmsNotification($user->getEmail(), '', $user,$request->request->get('password')['first'] ));


            $b=array(
                'result' =>1,
                'message' => 'success',
            );
            return new JsonResponse($b);
        }


    }
    /**
     * @Route("/user/{id}/update", name="update_user", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function update_user($id,Request $request, ValidatorInterface $validator, EntityManagerInterface $manager)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if($request->isXmlHttpRequest()) {
            $form =  $this->createForm(UserType::class);
            $form->handleRequest($request);
            if (!$form->isValid()) {

                $b=array(
                    'result' => 0,
                    'message' => 'Invalid form',
                    'data' => $this->getErrorsFromForm($form)
                );
                return new JsonResponse($b);
            }
            $user->setFirstName($request->request->get('first_name'));
            $user->setLastName($request->request->get('last_name'));

            $user->setemail($request->request->get('email'));

            //$user->setRoles($request->request->get('roles'));

            $fileName='';
            $file = $request->files->get ( 'picture' );
            if($file){
                $fileName = md5 ( uniqid () ) . '.' . $file->guessExtension ();
                $original_name = $file->getClientOriginalName ();
                $file->move ( $this->getParameter ( 'users_directory' ), $fileName );
                //$imageFile = $form->getData('picture');;
                //dd($imageFile);
                $user->setPicture( $fileName);

            }



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
     * @Route("/users/delete", name="delete_user", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_user(Request $request, ValidatorInterface $validator, EntityManagerInterface $manager)
    {


        $user = $this->getDoctrine()->getRepository(User::class)->find($request->request->get('id'));
        $manager->remove($user);
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
     * @Route("/users/add", name="add_user")
     */
    public function add_user()
    {
        $task = new User();

        $form =  $this->createForm(UserType::class);

        return $this->render('users/add_user.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/users/edit/{id}", name="edit_user")
     */
    public function edit_user($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);


        $form =  $this->createForm(UserType::class, $user);

        return $this->render('users/edit_user.html.twig', [
            'form' => $form->createView(),
            'user'=>$user
        ]);
    }


    /**
     * @Route("/users", name="users")
     */
    public function index(Request $request, DataTableFactory $dataTableFactory)
    {


        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(array(), array('id' => 'DESC') );
        $tab=[];
        foreach ($users as $user){
            $tab2=[];
            $tab2['id'] =  $user->getId();

            switch ($user->getRoles()[0]){
                case 'ROLE_ADMIN':$role="Admin";break;
                case 'ROLE_EMPLOYEE': $role='Employee'; break;
                default: $role='User';
            }
            $role=str_replace(',ROLE_USER' ,'', implode(',',$user->getRoles()));

            if($user->getPicture()!='')
                  $picture='<span class="avatar"><img src="http://localhost/quizz/public/users/'.$user->getPicture().'" alt=""></span>';
              else $picture='<span class="avatar"><img src="http://localhost/quizz/public/users/avatar.jpg'.'" alt=""></span>';
            $tab2['picture']=$picture;
            $tab2['name'] = $user->getFirstName().' '.$user->getLastName();

            $tab2['email'] = $user->getEmail();

            $tab2['role'] =$role=str_replace(',ROLE_USER' ,'', implode(',',$user->getRoles()));;


            $tab2['actions']='<div style="float: right">
<i class="fas fa-edit text-info ml-2 " style="cursor: pointer" onclick="edit_user('.$user->getId().')">
</i>
<i class="fas fa-trash-alt text-danger ml-2 "  style="cursor: pointer"  onclick="delete_user('.$user->getId().')"></i>
</div>';
            $tab[]=$tab2;
        }
        $table = $dataTableFactory->create([])
            ->add('id', TextColumn::class, ['label' => 'ID','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])

            ->add('picture', TextColumn::class, ['label' => 'Picture','render'=>'%s',  'raw' => true,'className' => 'bold','orderable' => true,'searchable' => true])

            ->add('name', TextColumn::class, ['label' => 'Name','render'=>'%s', 'className' => 'bold','orderable' => true,'searchable' => true])
            ->add('email', TextColumn::class, ['label' => 'Email', 'render' => '%s', 'raw' => true,'orderable' => true])
             ->add('role', TextColumn::class ,['label' => 'Role', 'render' => '<span class="badge bg-inverse-info">%s</span>', 'raw' => true])
            ->add('actions', TextColumn::class, ['label' => 'Actions', 'render' => '%s', 'raw' => true,'orderable' => false,'searchable' => false])
            ->createAdapter(ArrayAdapter::class, $tab)
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        return $this->render('users/index.html.twig', [
            'controller_name' => 'SecurityController',
            'datatable' => $table,
            'title'=>'Users'
        ]);
    }
}
