<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
            $client = $client->withOptions([
                'auth_basic' => [
                    'username' => $request->request->get('email'),
                    'password' => $request->request->get('password')
                ],
            ]);
            $response = $client->request(
                'GET',
                $_ENV['RESTAPI'].'/bank/test',
            );
            try {
                dd($response->getContent());
            } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
                dd($e->getMessage());
            }
        }
        return $this->render("login.html.twig");
    }
}
