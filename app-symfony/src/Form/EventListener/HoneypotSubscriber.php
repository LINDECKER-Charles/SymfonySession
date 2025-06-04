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

    public function onSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $form->get('honeytrap')->getData();
        $submittedData = [];

        foreach ($form as $child) {
            if($child->getName() !== "captcha"){
                $submittedData[$child->getName()] = $child->getData();
            }
            
        }

        $request = $this->requestStack->getCurrentRequest();
        
        $infoUser = [
            'ip' => $request?->getClientIp(), 
            'navigateur' => $request->headers->get('User-Agent'),
            'referrer' => $request->headers->get('Referer'),
            'uri' => $request->getRequestUri()
        ];


        if (!empty($data)) {
            $this->logger->warning('Tentative de spam détectée via le honeypot.', [
                'form' => $form->getName(),
                'userInfo' => $infoUser,
                'data' => $submittedData, // ou serialize si besoin
            ]);
        }
    }
}
