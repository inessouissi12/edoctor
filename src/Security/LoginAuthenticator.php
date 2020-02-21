<?php

namespace App\Security;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    private $passwordEncoder;
    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var UtilisateurRepository
     */
    private $UtilisateurRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManagerInterface $em, UtilisateurRepository $utilisateurRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
        $this->UtilisateurRepository =$utilisateurRepository;
    }
    public function supports(Request $request)
    {
       // return $request->get("_route") === "api_login" && $request->isMethod("POST");
    }
    public function getCredentials(Request $request)
    {
        dd($request);
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );
        $token = $extractor->extract($request);

        if (!$token) {
            return false;
        }
        return $token;
    }
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (JWTDecodeFailureException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }
        $username = $data['username'];
        return $this->UtilisateurRepository->findOneBy(['username' => $username]);
    }
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'error' => $exception->getMessageKey()
        ], 400);
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }
    public function start(Request $request, AuthenticationException $authException = null)
    {
    }
    public function supportsRememberMe()
    {
        return false;
    }

}
