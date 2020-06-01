<?php

class Extension extends Twig_Extension{
    public function getFunctions()
    {
        return[
            new Twig_SimpleFunction('activeClass', [$this, 'activeClass'], ['needs_context' => true])
        ];
    }

    public function activeClass( array $context, $page){
        if(isset($context['current_page']) && $context['current_page'] === $page){
            return ' active ';
        }
    }
}