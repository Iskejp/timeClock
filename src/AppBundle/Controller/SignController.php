<?php

/*
 * The MIT License
 *
 * Copyright 2017 David Yilma.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

//Forms
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

//Entities
use \AppBundle\Entity\User;
use AppBundle\Entity\Presence;

/**
 * Description of SignController
 *
 * @author David Yilma
 */
class SignController extends BasicController {
    
    const WORKING = 43200;
    const DAY = 86400;
    const MONTH = 2592000;

    /**
     * @Route("/sign/in", name="signIn")
     */
    public function inAction(Request $request) {

        //Check session and load data
        if($this->checkSession($request)) {
            $token = $this->cookies->get('session');
        } else {
            $token = FALSE;
        }
        
        //Get user code and search for user details
        $userCode = $request->get('code');
        $user = $this->findUser($userCode);
        if($user === null) {
            return $this->redirectToRoute('home');
        }
        
        if(!$this->isUserIn($token)) {
            //Get current time
            $now = new \DateTime('now');
            
            //Prepare unique token
            $nowString = $now->format('Y/m/d-H:i:s');
            $userId = $user->getId();
            $token = hash('sha256', $nowString.$userId);
            
            //Save token and user obejct into the cookie
            $response = new Response();
            $cookieSession = new Cookie('session', $token, time()+self::WORKING);
            //Permanent user cookies
            $cookieUser = new Cookie('user', $userCode, time()+self::MONTH);

            //Set cookies data and create cookies
            $response->headers->setCookie($cookieSession);
            $response->headers->setCookie($cookieUser);
            $response->send();
            
            //Prepare object for new presence
            $presence = new Presence();
            $presence->setType('work');
            $presence->setToken($token);
            $presence->setTimeIn($now);
            $presence->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($presence);

            //Save data to DB
            $em->flush();
        }
        
        return $this->redirectToRoute('dashboard');            
    }

    /**
     * @Route("/sign/up", name="signUp")
     */
    public function upAction(Request $request) {
        
        //Cleare cookies from browser.
        if($request->cookies->has('user')) {
            $response = new Response();
            $response->headers->clearCookie('user');
            $response->send();
        }
        
        $form = $this->createFormBuilder()
            ->add('code', TextType::class, array('label' => FALSE, 'attr' => array('placeholder' => 'Code Eg. 1111')))
            ->add('save', SubmitType::class, array('label' => 'Sign Me Up'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();
            
            $currentUser = $this->findUser($newUser['code']);
            if($currentUser === NULL) {
                //Prepare an object for new user
                //$user = new User();
                //$user->setName($newUser['name']);
                //$user->setEmail($newUser['email']);
                //$user->setCode($newUser['code']);
                //$user->setRole('visitor');
                //$user->setCreated(new \DateTime('now'));

                //$em = $this->getDoctrine()->getManager();
                //$em->persist($user);

                //Save data to the DB.
                //$em->flush();
                
                return $this->render('sign/up.html.twig', array(
                    'form' => $form->createView(),
                    'error' => 'Your code is not valid. Please contact IT Department.',
                ));
            }
            
            return $this->redirectToRoute('signIn', array('code' => $newUser['code']));
        }
        
        return $this->render('sign/up.html.twig', array(
            'form' => $form->createView(),
            'error' => FALSE,
        ));
    }

    /**
     * @Route("/sign/out", name="signOut")
     */
    public function outAction(Request $request) {
        
        //Check session and load data
        if($this->checkSession($request)) {
            $token = $this->cookies->get('session');
        } else {
            $this->redirectToRoute('home');
        }
        
        //Search for user details        
        $userCode = $this->cookies->get('user');
        $user = $this->findUser($userCode);
        if($user === null) {
            return $this->redirectToRoute('home');
        }
        
        //Prepare data
        $now = new \DateTime('now');
        
        //Prepare an object for new presence
        $em = $this->getDoctrine()->getManager();
        $session = $em->getRepository('AppBundle:Presence')->findOneBy(
            array('token' => $token, 'timeOut' => NULL)
        );

        if (!$session) {
            throw $this->createNotFoundException(
                'No session found.'
            );
        }
        
        //Figure out a time difference
        $timeIn = $session->getTimeIn();
        $timePeriod = $now->diff($timeIn)->format("%H:%I.%s");
        dump($timePeriod);
                
        //Update data
        $session->setTimeOut($now);
        $session->setTimePeriod($timePeriod);

        //Save data to the DB.
        $em->flush();
        
        //Cleare cookies from browser.
        $response = new Response();
        $response->headers->clearCookie('session');
        $response->send();
        
        //Send data to template
        return $this->render('sign/out.html.twig');
    }    
}
