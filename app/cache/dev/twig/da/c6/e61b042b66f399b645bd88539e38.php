<?php

/* TwigBundle:Form:div_layout.html.twig */
class __TwigTemplate_dac6e61b042b66f399b645bd88539e38 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'field_rows' => array($this, 'block_field_rows'),
            'field_enctype' => array($this, 'block_field_enctype'),
            'field_errors' => array($this, 'block_field_errors'),
            'field_rest' => array($this, 'block_field_rest'),
            'field_label' => array($this, 'block_field_label'),
            'attributes' => array($this, 'block_attributes'),
            'container_attributes' => array($this, 'block_container_attributes'),
            'field_widget' => array($this, 'block_field_widget'),
            'text_widget' => array($this, 'block_text_widget'),
            'password_widget' => array($this, 'block_password_widget'),
            'hidden_widget' => array($this, 'block_hidden_widget'),
            'hidden_row' => array($this, 'block_hidden_row'),
            'textarea_widget' => array($this, 'block_textarea_widget'),
            'options' => array($this, 'block_options'),
            'choice_widget' => array($this, 'block_choice_widget'),
            'checkbox_widget' => array($this, 'block_checkbox_widget'),
            'radio_widget' => array($this, 'block_radio_widget'),
            'datetime_widget' => array($this, 'block_datetime_widget'),
            'date_widget' => array($this, 'block_date_widget'),
            'time_widget' => array($this, 'block_time_widget'),
            'number_widget' => array($this, 'block_number_widget'),
            'integer_widget' => array($this, 'block_integer_widget'),
            'money_widget' => array($this, 'block_money_widget'),
            'url_widget' => array($this, 'block_url_widget'),
            'search_widget' => array($this, 'block_search_widget'),
            'percent_widget' => array($this, 'block_percent_widget'),
            'file_widget' => array($this, 'block_file_widget'),
            'collection_widget' => array($this, 'block_collection_widget'),
            'repeated_row' => array($this, 'block_repeated_row'),
            'field_row' => array($this, 'block_field_row'),
            'form_widget' => array($this, 'block_form_widget'),
            'email_widget' => array($this, 'block_email_widget'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        // line 1
        $this->displayBlock('field_rows', $context, $blocks);
        // line 9
        echo "
";
        // line 10
        $this->displayBlock('field_enctype', $context, $blocks);
        // line 15
        echo "
";
        // line 16
        $this->displayBlock('field_errors', $context, $blocks);
        // line 27
        echo "
";
        // line 28
        $this->displayBlock('field_rest', $context, $blocks);
        // line 37
        echo "
";
        // line 38
        $this->displayBlock('field_label', $context, $blocks);
        // line 43
        echo "
";
        // line 44
        $this->displayBlock('attributes', $context, $blocks);
        // line 50
        echo "
";
        // line 51
        $this->displayBlock('container_attributes', $context, $blocks);
        // line 57
        echo "    
";
        // line 58
        $this->displayBlock('field_widget', $context, $blocks);
        // line 64
        echo "
";
        // line 65
        $this->displayBlock('text_widget', $context, $blocks);
        // line 71
        echo "
";
        // line 72
        $this->displayBlock('password_widget', $context, $blocks);
        // line 78
        echo "
";
        // line 79
        $this->displayBlock('hidden_widget', $context, $blocks);
        // line 83
        echo "
";
        // line 84
        $this->displayBlock('hidden_row', $context, $blocks);
        // line 87
        echo "
";
        // line 88
        $this->displayBlock('textarea_widget', $context, $blocks);
        // line 93
        echo "
";
        // line 94
        $this->displayBlock('options', $context, $blocks);
        // line 109
        echo "
";
        // line 110
        $this->displayBlock('choice_widget', $context, $blocks);
        // line 135
        echo "
";
        // line 136
        $this->displayBlock('checkbox_widget', $context, $blocks);
        // line 141
        echo "
";
        // line 142
        $this->displayBlock('radio_widget', $context, $blocks);
        // line 147
        echo "
";
        // line 148
        $this->displayBlock('datetime_widget', $context, $blocks);
        // line 158
        echo "
";
        // line 159
        $this->displayBlock('date_widget', $context, $blocks);
        // line 174
        echo "
";
        // line 175
        $this->displayBlock('time_widget', $context, $blocks);
        // line 182
        echo "
";
        // line 183
        $this->displayBlock('number_widget', $context, $blocks);
        // line 190
        echo "
";
        // line 191
        $this->displayBlock('integer_widget', $context, $blocks);
        // line 197
        echo "
";
        // line 198
        $this->displayBlock('money_widget', $context, $blocks);
        // line 203
        echo "
";
        // line 204
        $this->displayBlock('url_widget', $context, $blocks);
        // line 210
        echo "
";
        // line 211
        $this->displayBlock('search_widget', $context, $blocks);
        // line 217
        echo "
";
        // line 218
        $this->displayBlock('percent_widget', $context, $blocks);
        // line 224
        echo "
";
        // line 225
        $this->displayBlock('file_widget', $context, $blocks);
        // line 235
        echo "
";
        // line 236
        $this->displayBlock('collection_widget', $context, $blocks);
        // line 241
        echo "
";
        // line 242
        $this->displayBlock('repeated_row', $context, $blocks);
        // line 247
        echo "

";
        // line 249
        $this->displayBlock('field_row', $context, $blocks);
        // line 258
        echo "
";
        // line 259
        $this->displayBlock('form_widget', $context, $blocks);
        // line 267
        echo "
";
        // line 268
        $this->displayBlock('email_widget', $context, $blocks);
    }

    // line 1
    public function block_field_rows($context, array $blocks = array())
    {
        // line 2
        ob_start();
        // line 3
        echo "    ";
        echo $this->env->getExtension('form')->renderErrors($this->getContext($context, 'form'));
        echo "
    ";
        // line 4
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'form'));
        foreach ($context['_seq'] as $context['_key'] => $context['child']) {
            // line 5
            echo "        ";
            echo $this->env->getExtension('form')->renderRow($this->getContext($context, 'child'));
            echo "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 10
    public function block_field_enctype($context, array $blocks = array())
    {
        // line 11
        ob_start();
        // line 12
        echo "    ";
        if ($this->getContext($context, 'multipart')) {
            echo "enctype=\"multipart/form-data\"";
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 16
    public function block_field_errors($context, array $blocks = array())
    {
        // line 17
        ob_start();
        // line 18
        echo "    ";
        if ((twig_length_filter($this->env, $this->getContext($context, 'errors')) > 0)) {
            // line 19
            echo "    <ul>
        ";
            // line 20
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'errors'));
            foreach ($context['_seq'] as $context['_key'] => $context['error']) {
                // line 21
                echo "            <li>";
                echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans($this->getAttribute($this->getContext($context, 'error'), "messageTemplate", array(), "any", false), $this->getAttribute($this->getContext($context, 'error'), "messageParameters", array(), "any", false), "validators"), "html");
                echo "</li>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 23
            echo "    </ul>
    ";
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 28
    public function block_field_rest($context, array $blocks = array())
    {
        // line 29
        ob_start();
        // line 30
        echo "    ";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'form'));
        foreach ($context['_seq'] as $context['_key'] => $context['child']) {
            // line 31
            echo "        ";
            if ((!$this->getAttribute($this->getContext($context, 'child'), "rendered", array(), "any", false))) {
                // line 32
                echo "            ";
                echo $this->env->getExtension('form')->renderRow($this->getContext($context, 'child'));
                echo "
        ";
            }
            // line 34
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 38
    public function block_field_label($context, array $blocks = array())
    {
        // line 39
        ob_start();
        // line 40
        echo "    <label for=\"";
        echo twig_escape_filter($this->env, $this->getContext($context, 'id'), "html");
        echo "\"";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'attr'));
        foreach ($context['_seq'] as $context['attrname'] => $context['attrvalue']) {
            echo " ";
            echo twig_escape_filter($this->env, $this->getContext($context, 'attrname'), "html");
            echo "=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, 'attrvalue'), "html");
            echo "\"";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['attrname'], $context['attrvalue'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo ">";
        echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans($this->getContext($context, 'label')), "html");
        echo "</label>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 44
    public function block_attributes($context, array $blocks = array())
    {
        // line 45
        ob_start();
        // line 46
        echo "    id=\"";
        echo twig_escape_filter($this->env, $this->getContext($context, 'id'), "html");
        echo "\" name=\"";
        echo twig_escape_filter($this->env, $this->getContext($context, 'full_name'), "html");
        echo "\"";
        if ($this->getContext($context, 'read_only')) {
            echo " disabled=\"disabled\"";
        }
        if ($this->getContext($context, 'required')) {
            echo " required=\"required\"";
        }
        if ($this->getContext($context, 'max_length')) {
            echo " maxlength=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, 'max_length'), "html");
            echo "\"";
        }
        if ($this->getContext($context, 'pattern')) {
            echo " pattern=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, 'pattern'), "html");
            echo "\"";
        }
        // line 47
        echo "    ";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'attr'));
        foreach ($context['_seq'] as $context['attrname'] => $context['attrvalue']) {
            echo twig_escape_filter($this->env, $this->getContext($context, 'attrname'), "html");
            echo "=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, 'attrvalue'), "html");
            echo "\" ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['attrname'], $context['attrvalue'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 51
    public function block_container_attributes($context, array $blocks = array())
    {
        // line 52
        ob_start();
        // line 53
        echo "    id=\"";
        echo twig_escape_filter($this->env, $this->getContext($context, 'id'), "html");
        echo "\" 
    ";
        // line 54
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'attr'));
        foreach ($context['_seq'] as $context['attrname'] => $context['attrvalue']) {
            echo twig_escape_filter($this->env, $this->getContext($context, 'attrname'), "html");
            echo "=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, 'attrvalue'), "html");
            echo "\" ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['attrname'], $context['attrvalue'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 58
    public function block_field_widget($context, array $blocks = array())
    {
        // line 59
        ob_start();
        // line 60
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "text")) : ("text"));
        // line 61
        echo "    <input type=\"";
        echo twig_escape_filter($this->env, $this->getContext($context, 'type'), "html");
        echo "\" ";
        echo $this->renderBlock("attributes", $context, $blocks);
        echo " value=\"";
        echo twig_escape_filter($this->env, $this->getContext($context, 'value'), "html");
        echo "\" />
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 65
    public function block_text_widget($context, array $blocks = array())
    {
        // line 66
        ob_start();
        // line 67
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "text")) : ("text"));
        // line 68
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 72
    public function block_password_widget($context, array $blocks = array())
    {
        // line 73
        ob_start();
        // line 74
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "password")) : ("password"));
        // line 75
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 79
    public function block_hidden_widget($context, array $blocks = array())
    {
        // line 80
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "hidden")) : ("hidden"));
        // line 81
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
    }

    // line 84
    public function block_hidden_row($context, array $blocks = array())
    {
        // line 85
        echo "    ";
        echo $this->env->getExtension('form')->renderWidget($this->getContext($context, 'form'));
        echo "
";
    }

    // line 88
    public function block_textarea_widget($context, array $blocks = array())
    {
        // line 89
        ob_start();
        // line 90
        echo "    <textarea ";
        echo $this->renderBlock("attributes", $context, $blocks);
        echo ">";
        echo twig_escape_filter($this->env, $this->getContext($context, 'value'), "html");
        echo "</textarea>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 94
    public function block_options($context, array $blocks = array())
    {
        // line 95
        ob_start();
        // line 96
        echo "    ";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'options'));
        foreach ($context['_seq'] as $context['choice'] => $context['label']) {
            // line 97
            echo "        ";
            if ($this->getAttribute($this->getContext($context, 'form'), "choiceGroup", array($this->getContext($context, 'label'), ), "method", false)) {
                // line 98
                echo "            <optgroup label=\"";
                echo twig_escape_filter($this->env, $this->getContext($context, 'choice'), "html");
                echo "\">
                ";
                // line 99
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'label'));
                foreach ($context['_seq'] as $context['nestedChoice'] => $context['nestedLabel']) {
                    // line 100
                    echo "                    <option value=\"";
                    echo twig_escape_filter($this->env, $this->getContext($context, 'nestedChoice'), "html");
                    echo "\"";
                    if ($this->getAttribute($this->getContext($context, 'form'), "choiceSelected", array($this->getContext($context, 'nestedChoice'), ), "method", false)) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    echo twig_escape_filter($this->env, $this->getContext($context, 'nestedLabel'), "html");
                    echo "</option>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['nestedChoice'], $context['nestedLabel'], $context['_parent'], $context['loop']);
                $context = array_merge($_parent, array_intersect_key($context, $_parent));
                // line 102
                echo "            </optgroup>
        ";
            } else {
                // line 104
                echo "            <option value=\"";
                echo twig_escape_filter($this->env, $this->getContext($context, 'choice'), "html");
                echo "\"";
                if ($this->getAttribute($this->getContext($context, 'form'), "choiceSelected", array($this->getContext($context, 'choice'), ), "method", false)) {
                    echo " selected=\"selected\"";
                }
                echo ">";
                echo twig_escape_filter($this->env, $this->getContext($context, 'label'), "html");
                echo "</option>
        ";
            }
            // line 106
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['choice'], $context['label'], $context['_parent'], $context['loop']);
        $context = array_merge($_parent, array_intersect_key($context, $_parent));
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 110
    public function block_choice_widget($context, array $blocks = array())
    {
        // line 111
        ob_start();
        // line 112
        echo "    ";
        if ($this->getContext($context, 'expanded')) {
            // line 113
            echo "        <div ";
            echo $this->renderBlock("container_attributes", $context, $blocks);
            echo ">
        ";
            // line 114
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getContext($context, 'form'));
            foreach ($context['_seq'] as $context['choice'] => $context['child']) {
                // line 115
                echo "            ";
                echo $this->env->getExtension('form')->renderWidget($this->getContext($context, 'child'));
                echo "
            ";
                // line 116
                echo $this->env->getExtension('form')->renderLabel($this->getContext($context, 'child'));
                echo "
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['choice'], $context['child'], $context['_parent'], $context['loop']);
            $context = array_merge($_parent, array_intersect_key($context, $_parent));
            // line 118
            echo "        </div>
    ";
        } else {
            // line 120
            echo "    <select ";
            echo $this->renderBlock("attributes", $context, $blocks);
            if ($this->getContext($context, 'multiple')) {
                echo " multiple=\"multiple\"";
            }
            echo ">
        ";
            // line 121
            if (((!$this->getContext($context, 'multiple')) && (!$this->getContext($context, 'required')))) {
                // line 122
                echo "            <option value=\"\">";
                echo twig_escape_filter($this->env, $this->getContext($context, 'empty_value'), "html");
                echo "</option>
        ";
            }
            // line 124
            echo "        ";
            if ((twig_length_filter($this->env, $this->getContext($context, 'preferred_choices')) > 0)) {
                // line 125
                echo "            ";
                $context['options'] = $this->getContext($context, 'preferred_choices');
                // line 126
                echo "            ";
                echo $this->renderBlock("options", $context, $blocks);
                echo "
            <option disabled=\"disabled\">";
                // line 127
                echo twig_escape_filter($this->env, $this->getContext($context, 'separator'), "html");
                echo "</option>
        ";
            }
            // line 129
            echo "        ";
            $context['options'] = $this->getContext($context, 'choices');
            // line 130
            echo "        ";
            echo $this->renderBlock("options", $context, $blocks);
            echo "
    </select>
    ";
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 136
    public function block_checkbox_widget($context, array $blocks = array())
    {
        // line 137
        ob_start();
        // line 138
        echo "    <input type=\"checkbox\" ";
        echo $this->renderBlock("attributes", $context, $blocks);
        if (twig_test_defined("value", $context)) {
            echo " value=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, 'value'), "html");
            echo "\"";
        }
        if ($this->getContext($context, 'checked')) {
            echo " checked=\"checked\"";
        }
        echo " />
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 142
    public function block_radio_widget($context, array $blocks = array())
    {
        // line 143
        ob_start();
        // line 144
        echo "    <input type=\"radio\" ";
        echo $this->renderBlock("attributes", $context, $blocks);
        if (twig_test_defined("value", $context)) {
            echo " value=\"";
            echo twig_escape_filter($this->env, $this->getContext($context, 'value'), "html");
            echo "\"";
        }
        if ($this->getContext($context, 'checked')) {
            echo " checked=\"checked\"";
        }
        echo " />
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 148
    public function block_datetime_widget($context, array $blocks = array())
    {
        // line 149
        ob_start();
        // line 150
        echo "    <div ";
        echo $this->renderBlock("container_attributes", $context, $blocks);
        echo ">
        ";
        // line 151
        echo $this->env->getExtension('form')->renderErrors($this->getAttribute($this->getContext($context, 'form'), "date", array(), "any", false));
        echo "
        ";
        // line 152
        echo $this->env->getExtension('form')->renderErrors($this->getAttribute($this->getContext($context, 'form'), "time", array(), "any", false));
        echo "
        ";
        // line 153
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "date", array(), "any", false));
        echo "
        ";
        // line 154
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "time", array(), "any", false));
        echo "
    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 159
    public function block_date_widget($context, array $blocks = array())
    {
        // line 160
        ob_start();
        // line 161
        echo "    ";
        if (($this->getContext($context, 'widget') == "single_text")) {
            // line 162
            echo "        ";
            echo $this->renderBlock("text_widget", $context, $blocks);
            echo "
    ";
        } else {
            // line 164
            echo "        <div ";
            echo $this->renderBlock("container_attributes", $context, $blocks);
            echo ">
            ";
            // line 165
            echo twig_strtr($this->getContext($context, 'date_pattern'), array("{{ year }}" => $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "year", array(), "any", false)), "{{ month }}" => $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "month", array(), "any", false)), "{{ day }}" => $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "day", array(), "any", false))));
            // line 169
            echo "
        </div>
    ";
        }
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 175
    public function block_time_widget($context, array $blocks = array())
    {
        // line 176
        ob_start();
        // line 177
        echo "    <div ";
        echo $this->renderBlock("container_attributes", $context, $blocks);
        echo ">
        ";
        // line 178
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "hour", array(), "any", false), array("attr" => array("size" => "1")));
        echo ":";
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "minute", array(), "any", false), array("attr" => array("size" => "1")));
        if ($this->getContext($context, 'with_seconds')) {
            echo ":";
            echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "second", array(), "any", false), array("attr" => array("size" => "1")));
        }
        // line 179
        echo "    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 183
    public function block_number_widget($context, array $blocks = array())
    {
        // line 184
        ob_start();
        // line 185
        echo "    ";
        // line 186
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "text")) : ("text"));
        // line 187
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 191
    public function block_integer_widget($context, array $blocks = array())
    {
        // line 192
        ob_start();
        // line 193
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "number")) : ("number"));
        // line 194
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 198
    public function block_money_widget($context, array $blocks = array())
    {
        // line 199
        ob_start();
        // line 200
        echo "    ";
        echo twig_strtr($this->getContext($context, 'money_pattern'), array("{{ widget }}" => $this->renderBlock("field_widget", $context, $blocks)));
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 204
    public function block_url_widget($context, array $blocks = array())
    {
        // line 205
        ob_start();
        // line 206
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "url")) : ("url"));
        // line 207
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 211
    public function block_search_widget($context, array $blocks = array())
    {
        // line 212
        ob_start();
        // line 213
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "search")) : ("search"));
        // line 214
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 218
    public function block_percent_widget($context, array $blocks = array())
    {
        // line 219
        ob_start();
        // line 220
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "text")) : ("text"));
        // line 221
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo " %
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 225
    public function block_file_widget($context, array $blocks = array())
    {
        // line 226
        ob_start();
        // line 227
        echo "    <div ";
        echo $this->renderBlock("container_attributes", $context, $blocks);
        echo ">
        ";
        // line 228
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "file", array(), "any", false));
        echo "
        ";
        // line 229
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "token", array(), "any", false));
        echo "
        ";
        // line 230
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "name", array(), "any", false));
        echo "
        ";
        // line 231
        echo $this->env->getExtension('form')->renderWidget($this->getAttribute($this->getContext($context, 'form'), "originalName", array(), "any", false));
        echo "
    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 236
    public function block_collection_widget($context, array $blocks = array())
    {
        // line 237
        ob_start();
        // line 238
        echo "    ";
        echo $this->renderBlock("form_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 242
    public function block_repeated_row($context, array $blocks = array())
    {
        // line 243
        ob_start();
        // line 244
        echo "    ";
        echo $this->renderBlock("field_rows", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 249
    public function block_field_row($context, array $blocks = array())
    {
        // line 250
        ob_start();
        // line 251
        echo "    <div>
        ";
        // line 252
        echo $this->env->getExtension('form')->renderLabel($this->getContext($context, 'form'));
        echo "
        ";
        // line 253
        echo $this->env->getExtension('form')->renderErrors($this->getContext($context, 'form'));
        echo "
        ";
        // line 254
        echo $this->env->getExtension('form')->renderWidget($this->getContext($context, 'form'));
        echo "
    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 259
    public function block_form_widget($context, array $blocks = array())
    {
        // line 260
        ob_start();
        // line 261
        echo "    <div ";
        echo $this->renderBlock("container_attributes", $context, $blocks);
        echo ">
        ";
        // line 262
        echo $this->renderBlock("field_rows", $context, $blocks);
        echo "
        ";
        // line 263
        echo $this->env->getExtension('form')->renderRest($this->getContext($context, 'form'));
        echo "
    </div>
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    // line 268
    public function block_email_widget($context, array $blocks = array())
    {
        // line 269
        ob_start();
        // line 270
        echo "    ";
        $context['type'] = ((twig_test_defined("type", $context)) ? (twig_default_filter($this->getContext($context, 'type'), "email")) : ("email"));
        // line 271
        echo "    ";
        echo $this->renderBlock("field_widget", $context, $blocks);
        echo "
";
        echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
    }

    public function getTemplateName()
    {
        return "TwigBundle:Form:div_layout.html.twig";
    }

    public function isTraitable()
    {
        return true;
    }
}
