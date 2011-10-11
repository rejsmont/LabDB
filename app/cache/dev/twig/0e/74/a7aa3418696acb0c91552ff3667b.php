<?php

/* FrameworkBundle:Exception:error.rdf.twig */
class __TwigTemplate_0e74a7aa3418696acb0c91552ff3667b extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        $this->env->loadTemplate("FrameworkBundle:Exception:error.xml.twig")->display(array_merge($context, array("exception" => $this->getContext($context, 'exception'))));
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:error.rdf.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
