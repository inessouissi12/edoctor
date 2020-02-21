<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class CheckAuthenticator extends AbstractGuardAuthenticator
{

    private $passwordEncoder;
    /**
     * @var EncoderInterface
     */
    private $Encoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder,
                                EncoderInterface $encoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->Encoder = $encoder;
    }

    public function supports(Request $request)
    {
        return $request->get("_route") === "api_login" && $request->isMethod("POST");
    }

    public function getCredentials(Request $request)
    {
        return array(
            'username' => $request->request->get("username"),
            'password' => $request->request->get("password")
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // if null, authentication will fail
        // if a User object, checkCredentials() is called
        return $userProvider->loadUserByUsername($credentials["username"]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }



    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );
        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new JsonResponse([
            'result' => true
        ]);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse([
            'error' => 'Access Denied'
        ]);

    }

    public function supportsRememberMe()
    {
        return false;
    }
}
