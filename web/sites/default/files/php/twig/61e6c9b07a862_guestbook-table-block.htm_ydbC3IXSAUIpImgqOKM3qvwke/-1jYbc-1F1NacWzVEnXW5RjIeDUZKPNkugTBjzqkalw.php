<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/custom/guestbook/templates/guestbook-table-block.html.twig */
class __TwigTemplate_71704415f81d6bc115f43328e608e1627455a7095010c06df5d861910446ab73 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<div class=\"table_revue\">
  <div class=\"user_info\">
    <div class=\"user_left\">
      <div class=\"revue_avatar\">";
        // line 4
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["avatar"] ?? null), 4, $this->source), "html", null, true);
        echo "</div>
    </div>
    <div class=\"user_right\">
      <p class=\"revue_name\">";
        // line 7
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["name"] ?? null), 7, $this->source), "html", null, true);
        echo "</p>
      <p class=\"revue_email\">";
        // line 8
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["email"] ?? null), 8, $this->source), "html", null, true);
        echo "</p>
      <p class=\"revue_phone\">";
        // line 9
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["phone"] ?? null), 9, $this->source), "html", null, true);
        echo "</p>
    ";
        // line 10
        if (twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "hasPermission", [0 => "administer nodes"], "method", false, false, true, 10)) {
            // line 11
            echo "      <div class=\"admin_menu\">
        <div class=\"user_buttons\">
          <div class=\"revue_button revue_table_edit\"><a href='/guestbook/editRevue/";
            // line 13
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["id"] ?? null), 13, $this->source), "html", null, true);
            echo "/' class=\"use-ajax revue_table_link\" data-dialog-type=\"modal\">Edit</a></div>
          <div class=\"revue_button revue_table_delete\"><a href='/guestbook/deleteRevue/";
            // line 14
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["id"] ?? null), 14, $this->source), "html", null, true);
            echo "/' class=\"use-ajax revue_table_link\" data-dialog-type=\"modal\">Delete</a></div>
        </div>
        <p class=\"revue_date\">";
            // line 16
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["date"] ?? null), 16, $this->source), "html", null, true);
            echo "</p>
      </div>
    ";
        }
        // line 19
        echo "    </div>
  </div>
  <div class=\"user_content\">
    <p class=\"textmessage\">";
        // line 22
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["textmessage"] ?? null), 22, $this->source), "html", null, true);
        echo "</p>
    <div class=\"image_wrapper\">";
        // line 23
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["image"] ?? null), 23, $this->source), "html", null, true);
        echo "</div>
  </div>

</div>
";
    }

    public function getTemplateName()
    {
        return "modules/custom/guestbook/templates/guestbook-table-block.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 23,  88 => 22,  83 => 19,  77 => 16,  72 => 14,  68 => 13,  64 => 11,  62 => 10,  58 => 9,  54 => 8,  50 => 7,  44 => 4,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/custom/guestbook/templates/guestbook-table-block.html.twig", "/var/www/web/modules/custom/guestbook/templates/guestbook-table-block.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 10);
        static $filters = array("escape" => 4);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
