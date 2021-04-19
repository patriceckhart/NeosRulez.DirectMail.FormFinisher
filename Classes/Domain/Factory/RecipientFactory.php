<?php
namespace NeosRulez\DirectMail\FormFinisher\Domain\Factory;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Scope("singleton")
 */
class RecipientFactory {

    /**
     * @Flow\Inject
     * @var \NeosRulez\DirectMail\Domain\Repository\RecipientRepository
     */
    protected $recipientRepository;

    /**
     * @Flow\Inject
     * @var \NeosRulez\DirectMail\Domain\Repository\RecipientListRepository
     */
    protected $recipientListRepository;

    /**
     * @Flow\Inject
     * @var \NeosRulez\DirectMail\Domain\Service\MailService
     */
    protected $mailService;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;


    /**
     * @param array $recipient
     * @return void
     */
    public function createRecipient(array $recipient):void
    {
        $newRecipient = new \NeosRulez\DirectMail\Domain\Model\Recipient();
        $newRecipient->setGender($recipient['gender']);
        $newRecipient->setFirstname($recipient['firstname']);
        $newRecipient->setLastname($recipient['lastname']);
        $newRecipient->setEmail($recipient['email']);

        $existingRecipient = $this->recipientRepository->findOneRecipientByMail($recipient['email']);

        if(!empty($existingRecipient)) {
            $recipientLists = $existingRecipient->getRecipientlist();
            $rawRecipientLists = [];
            foreach ($recipientLists as $list) {
                $rawRecipientLists[$this->persistenceManager->getIdentifierByObject($list)] = $list;
            }
            $rawRecipientLists[$recipient['recipientlist']] = $this->recipientListRepository->findRecipientListByIdentifier($recipient['recipientlist']);

            $existingRecipient->setRecipientlist($rawRecipientLists);

            $existingRecipient->setFirstname($newRecipient->getFirstname());
            $existingRecipient->setLastname($newRecipient->getLastname());
            $existingRecipient->setGender((int) $newRecipient->getGender());
            $existingRecipient->setCustomsalutation($newRecipient->getCustomsalutation());

            $this->recipientRepository->update($existingRecipient);
        } else {
            $newRecipient->setRecipientlist([$this->recipientListRepository->findRecipientListByIdentifier($recipient['recipientlist'])]);
            $newRecipient->setActive(false);
            $this->recipientRepository->add($newRecipient);
            $recipient['identifier'] = $this->persistenceManager->getIdentifierByObject($newRecipient);
            $this->sendMail($recipient);
        }
    }

    /**
     * @param array $recipient
     * @return void
     */
    public function sendMail(array $recipient):void
    {
        $recipient['customsalutation'] = '';
        $recipient['recipientIdentifier'] = $recipient['identifier'];
        $this->mailService->sendDoubleOptIn($recipient);
    }

}
