<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpMember;
use App\Database\Exceptions\MailChimpListNotFoundException;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mailchimp\Mailchimp;

class MembersController extends Controller
{
    /**
     * @var Mailchimp
     */
    private $mailChimp;

    /**
     * MembersController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Mailchimp $mailchimp
     */
    public function __construct(EntityManagerInterface $entityManager, Mailchimp $mailchimp)
    {
        parent::__construct($entityManager);

        $this->mailChimp = $mailchimp;
    }

    /**
     * Create MailChimp member
     *
     * @param Request $request
     * @param string $listId
     *
     * @return JsonResponse
     */
    public function create(Request $request, string $listId): JsonResponse
    {
        $list = $this->getListById($listId);
        if (!($list instanceof MailChimpList)) {
            return $this->getErrorResponseForListNotFound($listId);
        }
        // Instantiate entity
        $member = new MailChimpMember($request->all());
        // Validate entity
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse(
                [
                    'message' => 'Invalid data given',
                    'errors' => $validator->errors()->toArray()
                ]
            );
        }

        try {
            // Save member into db
            $this->saveEntity($member);
            // Save member into MailChimp
            $response = $this->mailChimp->post('lists/' . $list->getMailChimpId() . '/members', $member->toMailChimpArray());
            // Set MailChimp id on the member and save it into db
            $this->saveEntity($member->setMailChimpId($response->get('id')));
        } catch (\Exception $exception) {
            // Return error response if something goes wrong
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Retrieve and return MailChimp member.
     *
     * @param string $listId
     * @param string $memberId
     *
     * @return JsonResponse
     */
    public function show(string $listId, string $memberId): JsonResponse
    {
        $list = $this->getListById($listId);
        if (!($list instanceof MailChimpList)) {
            return $this->getErrorResponseForListNotFound($listId);
        }
        /** @var MailChimpMember|null $member */
        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);

        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
                404
            );
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * @param string $listId
     *
     * @return MailChimpList|null
     */
    private function getListById(string $listId): ?MailChimpList
    {
        return  $this->entityManager->getRepository(MailChimpList::class)->find($listId);
    }

    /**
     * @param string $listId
     *
     * @return JsonResponse
     */
    private function getErrorResponseForListNotFound(string $listId): JsonResponse
    {
        return $this->errorResponse(
            ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
            404
        );
    }
}
