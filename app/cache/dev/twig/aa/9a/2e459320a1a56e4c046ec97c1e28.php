<?php

/* TwigBundle:Exception:error.xml.twig */
class __TwigTemplate_aa9a2e459320a1a56e4c046ec97c1e28 extends Twig_Template
{
    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        echo "<?xml version=\"1.0\" encoding=\"";
        echo twig_escape_filter($this->env, $this->env->getCharset(), "html");
        echo "\" ?>

<error code=\"";
        // line 3
        echo twig_escape_filter($this->env, $this->getContext($context, 'status_code'), "html");
        echo "\" message=\"";
        echo twig_escape_filter($this->env, $this->getContext($context, 'status_text'), "html");
        echo "\" />
";
    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:error.xml.twig";
    }

    public function isTraitable()
    {
        return false;
    }
}
