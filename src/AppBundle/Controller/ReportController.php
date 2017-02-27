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
use \Symfony\Component\HttpFoundation\Request;

/**
 * Description of ReportController
 *
 * @author David Yilma
 */
class ReportController extends BasicController {
    
    /**
     * @Route("/report/peopleIn", name="peopleIn")
     */
    public function peopleInAction(Request $request) {
        $personalAnalytics = array();
        $overallAnalytics = array();
        $hrs = $min = $sec = 0;
        
        //Check session redirect if doesn't exist
        if(!$this->checkSession($request)) {
            return $this->redirectToRoute('home');
        }
        
        //Search all signed in users
        $peopleIn = $this->getDoctrine()->getRepository('AppBundle:Presence')->findAllUsersInWork();
        
        //Personal analytics
        foreach($peopleIn as $person) {
            //Get current time and user time
            $now = new \DateTime('now');
            $time = $person->getTimeIn();
            $personalAnalytics[$person->getId()]['period'] = $now->diff($time)->format('%H:%I.%S');
            
            //Workout totals
            $hrs += (int)$now->diff($time)->format('%H');
            $min += (int)$now->diff($time)->format('%i');
            $sec += (int)$now->diff($time)->format('%s');
        }
        
        //Overall anamytics
        $overallAnalytics['totalSecs'] = ($hrs*3600)+($min*60)+$sec;
        $overallAnalytics['totalHours'] = (($hrs*3600)+($min*60)+$sec)/3600;
        
        return $this->render('report/peopleIn.html.twig', array(
            'peopleIn' => $peopleIn,
            'personalAnalytics' => $personalAnalytics,
            'overallAnalytics' => $overallAnalytics,
        ));     
    }
    
    /**
     * @Route("/report/workload", name="workload")
     */
    
    public function workloadAction(Request $request) {
        $sessionTotal = 0;
        
        //$now = new \DateTime('now');        
        //$lastMonday = new \DateTime(strtotime('last Monday'));
        
        //Check session redirect if doesn't exist
        if(!$this->checkSession($request)) {
            return $this->redirectToRoute('home');
        }
        
        //Search for workload
        $workloads = $this->getDoctrine()->getRepository('AppBundle:Presence')->findWorkload();
        
        foreach($workloads as $workload) {
            dump($workload);
            $sessionTotal += $workload['sessions'];
        }
        
        return $this->render('report/workload.html.twig', array(
            'workLoads' => $workloads,
            'sessionTotal' => $sessionTotal,
        ));     
    }
}
