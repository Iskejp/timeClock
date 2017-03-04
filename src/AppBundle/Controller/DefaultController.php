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
 * Description of DefaultController
 *
 * @author David Yilma
 */
class DefaultController extends BasicController {

    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request) {
        
        //Check session and load data
        $token = $this->readSession($request, 'token');
        if(!$token) {
            $userCode = $this->readUserCookie($request);
            return $this->render('default/index.html.twig', array(
                'code' => $userCode,
            ));
        }
        
        if($this->isUserIn($token)) {
            return $this->redirectToRoute('dashboard');
        } else {
            $userCode = $this->readUserCookie($request);
            return $this->render('default/index.html.twig', array(
                'code' => $userCode,
            ));
        }
    }
}
