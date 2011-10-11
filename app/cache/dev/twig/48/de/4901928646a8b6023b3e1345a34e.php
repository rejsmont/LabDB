<?php

/* FrameworkBundle:Exception:exception.atom.twig */
class __TwigTemplate_48de4901928646a8b6023b3e1345a34e extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        $this->env->loadTemplate("FrameworkBundle:Exception:exception.xml.twig")->display(array_merge($context, array("exception" => $this->getContext($context, 'exception'))));
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:exception.atom.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
