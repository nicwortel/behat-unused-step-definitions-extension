<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;

class FeatureContext implements Context
{
    /**
     * @Given some precondition
     * @Given some precondition that is never used in a feature
     */
    public function somePrecondition(): void
    {
    }

    /**
     * @When some action by the actor
     */
    public function someActionByTheActor(): void
    {
    }

    /**
     * @Then some testable outcome is achieved
     */
    public function someTestableOutcomeIsAchieved(): void
    {
    }

    /**
     * @Then something else we can check happens too
     */
    public function somethingElseWeCanCheckHappensToo(): void
    {
    }

    /**
     * @Then some step that is never used by a feature
     */
    public function someStepThatIsNeverUsedByAFeature(): void
    {
    }
}
