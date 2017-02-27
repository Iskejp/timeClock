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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of BasicController
 *
 * @author David Yilma
 */
class BasicController extends Controller {

    /**
     * Cookies data
     * @var type Symfony\Component\HttpFoundation\Request
     */
    protected $cookies;
    
    public function setCookies(Request $request) {
        $this->cookies = $request->cookies;
    }
    
    public function getCookies() {
        return $this->cookies;
    }

    protected function checkSession(Request $request) {
        
        $this->setCookies($request);
        
        if($request->cookies->has('session')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    protected function readUserCookie(Request $request) {
        
        $this->setCookies($request);
        
        if($request->cookies->has('user')) {
            return $request->cookies->get('user');
        } else {
            return FALSE;
        }
    }


    protected function findUser($userCode = null) {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(
                array('code' => $userCode));
        
        return $user;
    }
    
    protected function isUserIn($token) {
        if($token) {
            $presence = $this->getDoctrine()->getRepository('AppBundle:Presence')->findOneBy(
                    array('type' => 'out', 'token' => $token)
            );
            
            return ($presence) ? FALSE : TRUE;
        } else {
            return false;
        }
    }
}
