<?php
namespace NeosRulez\DirectMail\FormFinisher\Finishers;

/*
 * This file is part of the NeosRulez.DirectMail.FormFinisher package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\AbstractFinisher;

/**
 * This finisher saves the recipient in recipient list
 */
class AddRecipientFinisher extends AbstractFinisher
{

    /**
     * @Flow\Inject
     * @var \NeosRulez\DirectMail\FormFinisher\Domain\Factory\RecipientFactory
     */
    protected $recipientFactory;


    /**
     * Executes this finisher
     * @return void
     * @throws FinisherException
     * @see AbstractFinisher::execute()
     *
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formValues = $formRuntime->getFormState()->getFormValues();

        $doubleOptIn = true;
        $finishers = $formRuntime->getFormDefinition()->getFinishers();
        foreach ($finishers as $finisher) {
            $options = $finisher->options;
            if(array_key_exists('doubleOptIn', $options)) {
                if($options['doubleOptIn'] === false) {
                    $doubleOptIn = false;
                }
            }

        }

        $recipient = [];
        foreach ($formValues as $i => $value) {
            $recipient[$i] = $value;
        }

        if(!array_key_exists('firstname', $recipient)) {
            $recipient['firstname'] = '';
        }
        if(!array_key_exists('lastname', $recipient)) {
            $recipient['lastname'] = '';
        }
        if(!array_key_exists('gender', $recipient)) {
            $recipient['gender'] = 3;
        }
        if(!array_key_exists('email', $recipient)) {
            throw new \Neos\Flow\Exception('Required form field email (with identifier email) is not definied.', 1347145544);
        }
        if(!array_key_exists('recipientlist', $recipient)) {
            throw new \Neos\Flow\Exception('Required form field recipientlist is not definied.', 1347145544);
        }
        if(array_key_exists('acceptDirectMail', $recipient)) {
            if($recipient['acceptDirectMail'] == '1') {
                $this->recipientFactory->createRecipient($recipient, $doubleOptIn);
            }
        } else {
            throw new \Neos\Flow\Exception('Required form field acceptDirectMail is not definied.', 1347145544);
        }
    }

}
