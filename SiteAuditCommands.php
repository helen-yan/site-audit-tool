<?php

namespace Drush\Commands\site_audit_tool;

use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\OutputFormatters\StructuredData\RowsOfFieldsWithMetadata;
use Consolidation\SiteAlias\SiteAliasManagerAwareInterface;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;
use SiteAudit\SiteAuditCheckInterface;
use Symfony\Component\Console\Input\InputInterface;

// For testing
use SiteAudit\SiteAuditCheckBase;
use SiteAudit\Check\BestPracticesSettings;

/**
 * Edit this file to reflect your organization's needs.
 */
class SiteAuditCommands extends DrushCommands implements SiteAliasManagerAwareInterface
{
    use SiteAliasManagerAwareTrait;

    /**
     * @hook init
     *
     * Autoload our files if they are not already loaded.
     * Drush should do this as a service for global commands based
     * off of the information in composer.json. At the moment,
     * though, it does not.
     *
     * n.b. this hook runs when any command in this file is executed.
     */
    public function init()
    {
        if (!class_exists(SiteAuditCheckBase::class)) {
            $loader = new \Composer\Autoload\ClassLoader();
            $loader->addPsr4('SiteAudit\\', __DIR__ . '/src');
            $loader->register();
        }
    }

    /**
     * @command audit:best-practices
     * @aliases audit-best-practices,abp
     * @field-labels
     *     label: Label
     *     description: Description
     *     result: Result
     *     action: Action
     *     score: Score
     * @return RowsOfFieldsWithMetadata
     *
     * @param string $param A parameter
     * @bootstrap full
     *
     * Demonstrates a trivial command that takes a single required parameter.
     */
    public function bestPractices($param = '', $options = ['format' => 'json'])
    {
        $check = new BestPracticesSettings();

        // Temporary code to be thrown away
        $report = $this->interimReport('Best Practices', [$check]);

        // Note that we could improve the table output with the annotation
        //   @default-fields description,result,action
        // This would also by default hide the remaining fields in json
        // format, though, which would not be desirable.
        // @todo: Add a separate 'default-fields' for human-readable output,
        // or maybe always ignore it in unstructured output modes.
        return (new RowsOfFieldsWithMetadata($report))
            ->setDataKey('checks');
    }

    /**
     * Temporary code to be thrown away
     */
    protected function interimReport($label, $checks)
    {
        $score = 0;
        $max = 0;
        $checkResults = [];

        foreach ($checks as $check) {
            $max += 2;
            $score += $check->getScore();
            $checkResults += $this->interimReportResults($check);
        }

        $percent = ($score * 100) / $max;

        return [
            "percent" => (int) $percent,
            "label" => $label,
            "checks" => $checkResults,
        ];
    }

    /**
     * Temporary code to be thrown away
     */
    protected function interimReportResults(SiteAuditCheckInterface $check)
    {
        $checkName = $this->getCheckName($check);
        return [
            $checkName => [
                "label" => $check->getLabel(),
                "description" => $check->getDescription(),
                "result" => $check->getResult(),
                "action" => $check->renderAction(),
                "score" => $check->getScore(),
            ],
        ];
    }

    /**
     * Temporary code to be thrown away
     */
    protected function getCheckName(SiteAuditCheckInterface $check)
    {
        $full_class_name = get_class($check);
        return str_replace('\\', '', $full_class_name);
    }
}