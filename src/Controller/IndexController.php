<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IndexController extends AbstractController
{

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
//        if($this->getUser()==null || !in_array("ROLE_USER",$this->getUser()->getRoles())){
//            return $this->redirectToRoute("app_login");
//        }
        return $this->render('index.html.twig', [
            "accountbank"=>["amount"=>750,"iban"=>"FR7165434524251078502727126"],
            "user"=>["firstname"=>"Steve","name"=>"HOAREAU"]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/login',name: 'app_login')]
    public function login(Request $request, HttpClientInterface $client): Response
    {
        if($request->request->has('email') && $request->request->has('password')){
            $oneClient = clone $client;
            $oneClient = $oneClient->withOptions([
                'auth_basic' => [
                    'username' => $request->request->get('email'),
                    'password' => $request->request->get('password')
                ],
                'body'=>'{"email":"'.$request->request->get('email').'"}',
                "headers"=>array('Content-Type' => 'application/json')
            ]);
            $response = $oneClient->request(
                'GET',
                $_ENV['RESTAPI'].'/secret'
            );
            try {
                $token = $response->getContent();
                //on récupère le sel du client
                $oneClient = clone $client;
                $oneClient = $oneClient->withOptions([
                    'auth_bearer' => $token,
                    'body'=>'{"email":"'.$request->request->get('email').'"}',
                    "headers"=>array('Content-Type' => 'application/json')
                ]);
                $response = $oneClient->request(
                    'GET',
                    $_ENV['RESTAPI'].'/secret/'.$_ENV['PRIVATE_SALTED_ROOT']
                );
                dd($response->getContent());
                return $this->render('index.html.twig', [
                    "accountbank"=>["amount"=>750,"iban"=>"FR7165434524251078502727126"],
                    "user"=>["firstname"=>"Steve","name"=>"HOAREAU"]
                ]);
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                dd($client,$e->getMessage());
            }
        }
        return $this->render("login.html.twig");
    }
}
