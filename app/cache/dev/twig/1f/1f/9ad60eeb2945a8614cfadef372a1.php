<?php

/* FrameworkBundle:Exception:error.atom.twig */
class __TwigTemplate_1f1f9ad60eeb2945a8614cfadef372a1 extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        $this->env->loadTemplate("FrameworkBundle:Exception:error.xml.twig")->display(array_merge($context, array("exception" => $this->getContext($context, 'exception'))));
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:error.atom.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
