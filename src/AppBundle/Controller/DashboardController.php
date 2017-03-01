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

/**
 * Description of DashboardController
 *
 * @author David Yilma
 */
class DashboardController extends BasicController
{
    /**
     * @Route("/dashboard/default", name="dashboard")
     */
    public function defaultAction(Request $request) 
    {
        $timeTotal = 0;
        
        //Check session and load data
        if($this->checkSession($request))
        {
            $token = $this->cookies->get('session');
        }
        else
        {
            return $this->redirectToRoute('home');
        }
        
        //Get user code and search for user details
        $userCode = $this->cookies->get('user');
        $user = $this->findUser($userCode);
        if($user === NULL)
        {
            return $this->redirectToRoute('home');
        }
        
        if($this->isUserIn($token))
        {
            
            //Get current time
            $now = new \DateTime('now');
            
            //Search for presence
            $presence = $this->getDoctrine()->getRepository('AppBundle:Presence')->findOneBy(
                    array('type' => 'work', 'token' => $token)
            );
            
            if($presence === NULL)
            {
                return $this->redirectToRoute('home');  
            }
            
            $userTime = $presence->getTimeIn();
            $userInterval = $now->diff($userTime);
            
            //Search for active users
            $peopleIn = $this->whoIsIn();
            
            //Generate date for JQCloud - People In
            foreach($peopleIn as $person)
            {
                //Read sign in time and count interval
                $time = $person->getTimeIn();
                $interval = $now->diff($time);

                $names[] = array('text' => $person->getUser()->getName(), 'weight' => $interval->format('%i'));
            }
            
            //Search for workload
            $workloads = $this->schoolWorkload();
            
            //Generate Data for JQCloud - Workload
            foreach($workloads as $workload)
            {
                $workTimes[] = array('text' => $workload['timeDiff'], 'weight' => $workload['sessions']);
                $timeTotal += $workload['timeDiffSec'];                
            }
            
            return $this->render('dashboard/default.html.twig', array(
                'time' => $userTime,
                'interval' => $userInterval->format('%d days %h hours %i minutes %s seconds'),
                'names' => $names,
                'workTimes' => $workTimes,
                'timeTotal' => $this->secondsToString($timeTotal),
                'user' => $user,
            ));
        }
        else
        {
            return $this->redirectToRoute('home');            
        }
        
    }
    
    private function whoIsIn()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Presence')->findAllUsersInWork();
        return $users;
    }
    
    private function schoolWorkload()
    {
        $workloads = $this->getDoctrine()->getRepository('AppBundle:Presence')->findWorkload();
        return $workloads;
    }
}
