<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpMember;
use App\Database\Exceptions\MailChimpListNotFoundException;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
        } catch (Exception $exception) {
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

        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);
        if (!($member instanceof MailChimpMember)) {
            return $this->getErrorResponseForMemberNotFound($memberId);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Remove MailChimp member.
     *
     * @param string $listId
     * @param string $memberId
     *
     * @return JsonResponse
     */
    public function remove(string $listId, string $memberId): JsonResponse
    {
        $list = $this->getListById($listId);
        if (!($list instanceof MailChimpList)) {
            return $this->getErrorResponseForListNotFound($listId);
        }

        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);
        if (!($member instanceof MailChimpMember)) {
            return $this->getErrorResponseForMemberNotFound($memberId);
        }

        try {
            // Remove member from database
            $this->removeEntity($member);
            // Remove member from MailChimp
            $this->mailChimp->delete(\sprintf('lists/%s/members/%s', $list->getMailChimpId(), $member->getMailChimpId()));
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse([]);
    }

    /**
     * Update MailChimp member.
     *
     * @param Request $request
     * @param string $listId
     * @param string $memberId
     *
     * @return JsonResponse
     */
    public function update(Request $request, string $listId, string $memberId): JsonResponse
    {
        $list = $this->getListById($listId);
        if (!($list instanceof MailChimpList)) {
            return $this->getErrorResponseForListNotFound($listId);
        }

        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);
        if (!($member instanceof MailChimpMember)) {
            return $this->getErrorResponseForMemberNotFound($memberId);
        }

        // Update member properties
        $member->fill($request->all());

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
            // Update member into database
            $this->saveEntity($member);
            // Update member into MailChimp
            $this->mailChimp->patch(\sprintf('lists/%s/members/%s', $list->getMailChimpId(), $member->getMailChimpId()), $member->toMailChimpArray());
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
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

    /**
     * @param string $memberId
     *
     * @return JsonResponse
     */
    private function getErrorResponseForMemberNotFound(string $memberId): JsonResponse
    {
        return $this->errorResponse(
            ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
            404
        );
    }
}
