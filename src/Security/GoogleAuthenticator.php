<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AbstractOAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

class GoogleAuthenticator extends AbstractOAuth2Authenticator
{
    protected string $serviceName = "google" ;

    protected function getUserFromRessourceProvider(ResourceOwnerInterface $resourceOwner, UserRepository  $userRepository): ?User
    {
        if( !($resourceOwner instanceof GoogleUser ) ){
            throw new \RuntimeException("expecting google user", 1);
        }
        
        //dd($resourceOwner);
        if( true ==! ($resourceOwner->toArray()['email_verified']) ?? null ){
            throw new AuthenticationException(" L'email n'a pas été confirmé.") ;
        }

        $existingUser = $userRepository->findOneBy([
            'googleId' => $resourceOwner->getId() ,
            'email'    => $resourceOwner->getEmail()
        ]);

        return $existingUser ;
    }

    
}
