<?php

namespace App\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class HoneypotSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private RequestStack $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }


    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onSubmit',
        ];
    }
    /**
     * Détecte les tentatives de spam via le champ honeypot lors de la soumission d’un formulaire.
     *
     * @param FormEvent $event L’événement de soumission du formulaire.
     */
    public function onSubmit(FormEvent $event): void
    {
        /* On récupère formulaire + champ honeytrap */
        $form = $event->getForm();
        $data = $form->get('honeytrap')->getData();
        
        /* Avant de faire d'autre opération on vérifi si le honeypot est remplit ou non */
        if (!empty($data)) {
            /* On récupère l'intégralité des champs */
            $submittedData = [];
            foreach ($form as $child) {
                if($child->getName() !== "captcha"){ /* En exclu le captcha pour ne pas poluer le journal de log */
                    $submittedData[$child->getName()] = $child->getData();
                }
                
            }

            /* On récupère le requet HTTP */
            $request = $this->requestStack->getCurrentRequest(); 
            
            /* On récupère les informations souhaiter, à votre convenance de rajouter celle que vous souhaiter */
            $infoUser = [
                'ip' => $request?->getClientIp(), 
                'navigateur' => $request->headers->get('User-Agent'),
                'referrer' => $request->headers->get('Referer'),
                'uri' => $request->getRequestUri()
            ];
            /* On log la détéction du bot avec ces infos */
            $this->logger->warning('Tentative de spam détectée via le honeypot.', [
                'form' => $form->getName(),
                'userInfo' => $infoUser,
                'data' => $submittedData,
            ]);
        }
    }
}
