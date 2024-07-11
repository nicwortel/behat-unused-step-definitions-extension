<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;

class FeatureContextBase implements Context
{
    /**
     * @Then some step defined in the base class that is never used by a feature
     */
    public function someBaseClassStepThatIsNeverUsedByAFeature(): void
    {
    }
}
