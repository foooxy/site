<?php

/* index.html */
class __TwigTemplate_8d379530dd530e9a6562aec7f95bc4e010dd68ef8ea6bb39908d2c2557588ab9 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "﻿
<div class=\"container\">
\t<table class=\"table table-striped\">
\t\t<thead>
\t\t\t<tr>
\t\t\t\t<th>#</th>
\t\t\t\t<th>Gl</th>
\t\t\t\t<th>Г…Г¤ГЁГ­ГЁГ¶Г  ГЁГ§Г¬ГҐГ°ГҐГ­ГЁГї</th>
\t\t\t</tr>
\t\t</thead>
\t\t<tbody>
<!--\t\t\t";
        // line 12
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["rows"]) ? $context["rows"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 13
            echo "\t\t\t\t<tr>
\t\t\t\t\t<th>";
            // line 14
            echo twig_escape_filter($this->env, $this->getAttribute($context["row"], "id", array()), "html", null, true);
            echo "</th>
\t\t\t\t\t<td>";
            // line 15
            echo twig_escape_filter($this->env, $this->getAttribute($context["row"], "name", array()), "html", null, true);
            echo "</td>
\t\t\t\t\t<td>";
            // line 16
            echo twig_escape_filter($this->env, $this->getAttribute($context["row"], "type", array()), "html", null, true);
            echo "</td>
\t\t\t\t</tr>
\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['row'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 18
        echo " -->
\t\t</tbody>
\t</table>
</div>
<div class=\"row\">
\t<form method=\"post\">
\t\t<div class=\"col-lg-2\">
\t\t\t<div class=\"form-group\">
\t\t\t\t<input name=\"name\" type=\"text\" class=\"form-control\" placeholder=\"ГЏГ Г°Г Г¬ГҐГІГ°\">
\t\t\t</div>
\t\t</div>
\t\t<div class=\"col-lg-2\">
\t\t\t<div class=\"form-group\">
\t\t\t\t<input name=\"type\" type=\"text\" class=\"form-control\" placeholder=\"Г…Г¤ГЁГ­ГЁГ¶Г  ГЁГ§Г¬ГҐГ°ГҐГ­ГЁГї\">
\t\t\t</div>
\t\t</div>
\t\t<div class=\"col-lg-2\">
\t\t\t<div class=\"form-group\">
\t\t\t\t<input type=\"submit\" class=\"btn btn-default\" value=\"Г„Г®ГЎГ ГўГЁГІГј\">
\t\t\t</div>
\t\t</div>
\t</form>
</div>
";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  56 => 18,  47 => 16,  43 => 15,  39 => 14,  36 => 13,  32 => 12,  19 => 1,);
    }
}
/* ﻿*/
/* <div class="container">*/
/* 	<table class="table table-striped">*/
/* 		<thead>*/
/* 			<tr>*/
/* 				<th>#</th>*/
/* 				<th>Gl</th>*/
/* 				<th>Г…Г¤ГЁГ­ГЁГ¶Г  ГЁГ§Г¬ГҐГ°ГҐГ­ГЁГї</th>*/
/* 			</tr>*/
/* 		</thead>*/
/* 		<tbody>*/
/* <!--			{% for row in rows %}*/
/* 				<tr>*/
/* 					<th>{{ row.id }}</th>*/
/* 					<td>{{ row.name }}</td>*/
/* 					<td>{{ row.type }}</td>*/
/* 				</tr>*/
/* 			{% endfor %} -->*/
/* 		</tbody>*/
/* 	</table>*/
/* </div>*/
/* <div class="row">*/
/* 	<form method="post">*/
/* 		<div class="col-lg-2">*/
/* 			<div class="form-group">*/
/* 				<input name="name" type="text" class="form-control" placeholder="ГЏГ Г°Г Г¬ГҐГІГ°">*/
/* 			</div>*/
/* 		</div>*/
/* 		<div class="col-lg-2">*/
/* 			<div class="form-group">*/
/* 				<input name="type" type="text" class="form-control" placeholder="Г…Г¤ГЁГ­ГЁГ¶Г  ГЁГ§Г¬ГҐГ°ГҐГ­ГЁГї">*/
/* 			</div>*/
/* 		</div>*/
/* 		<div class="col-lg-2">*/
/* 			<div class="form-group">*/
/* 				<input type="submit" class="btn btn-default" value="Г„Г®ГЎГ ГўГЁГІГј">*/
/* 			</div>*/
/* 		</div>*/
/* 	</form>*/
/* </div>*/
/* */
