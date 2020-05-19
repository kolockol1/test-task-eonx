<?php
declare(strict_types=1);

namespace Functional\Http\Controllers\MailChimp;

use Tests\App\TestCases\MailChimp\MemberTestCase;

class MemberControllerTest extends MemberTestCase
{
    /**
     * Test application creates successfully member and returns it back with id from MailChimp.
     *
     * @return void
     */
    public function testCreateMemberSuccessfully(): void
    {
        $listContent = $this->generateList();

        // create member, by list_id
        $this->post(\sprintf('/mailchimp/lists/%s/members', $listContent['list_id']), static::$memberData);
        $memberContent = \json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->seeJson(static::$memberData);
        self::assertArrayHasKey('mail_chimp_id', $memberContent);
        self::assertNotNull($memberContent['mail_chimp_id']);
    }

    /**
     * Test application returns error response with errors when list validation fails.
     *
     * @return void
     */
    public function testCreateMemberValidationFailed(): void
    {
        $listContent = $this->generateList();

        // create member, by list_id
        $this->post(\sprintf('/mailchimp/lists/%s/members', $listContent['list_id']));
        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertEquals('Invalid data given', $content['message']);

        foreach (\array_keys(static::$memberData) as $key) {
            if (\in_array($key, static::$notRequiredForMember, true)) {
                continue;
            }

            self::assertArrayHasKey($key, $content['errors']);
        }
    }

    /**
     * Test application returns error response for MembersController::create endpoint when list not found.
     *
     * @return void
     */
    public function testCreateListForMemberNotFoundException(): void
    {
        $this->post(\sprintf('/mailchimp/lists/%s/members','invalid-list-id'));

        $this->assertListNotFoundResponse('invalid-list-id');
    }


    /**
     * Test application returns successful response with specific member data in list.
     *
     * @return void
     */
    public function testShowMemberSuccessfully(): void
    {
        $listContent = $this->generateList();
        $member = $this->createMember(static::$memberData);

        $this->get(\sprintf('/mailchimp/lists/%s/members/%s', $listContent['list_id'], $member->getId()));
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (static::$memberData as $key => $value) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals($value, $content[$key]);
        }
    }

    /**
     * Test application returns error response when member not found.
     *
     * @return void
     */
    public function testShowMemberNotFoundException(): void
    {
        $listContent = $this->generateList();
        $this->get(\sprintf('/mailchimp/lists/%s/members/%s', $listContent['list_id'], 'invalid-member-id'));

        $this->assertMemberNotFoundResponse('invalid-member-id');
    }

    /**
     * Test application returns error response for MembersController::show endpoint when list not found.
     *
     * @return void
     */
    public function testShowListForMemberNotFoundException(): void
    {
        $this->get(\sprintf('/mailchimp/lists/%s/members/%s', 'invalid-list-id', 'invalid-member-id'));

        $this->assertListNotFoundResponse('invalid-list-id');
    }

    /**
     * Test application returns empty successful response when removing existing member.
     *
     * @return void
     */
    public function testRemoveMemberSuccessfully(): void
    {
        $listContent = $this->generateList();

        $this->post(\sprintf('/mailchimp/lists/%s/members', $listContent['list_id']), static::$memberData);
        $memberContent = \json_decode($this->response->getContent(), true);

        $this->delete(\sprintf('/mailchimp/lists/%s/members/%s', $listContent['list_id'], $memberContent['member_id']));

        $this->assertResponseOk();
        self::assertEmpty(\json_decode($this->response->content(), true));
    }


    /**
     * Test application returns error response when member not found.
     *
     * @return void
     */
    public function testRemoveMemberNotFoundException(): void
    {
        $listContent = $this->generateList();
        $this->delete(\sprintf('/mailchimp/lists/%s/members/%s', $listContent['list_id'], 'invalid-member-id'));

        $this->assertMemberNotFoundResponse('invalid-member-id');
    }

    /**
     * Test application returns error response for MembersController::show endpoint when list not found.
     *
     * @return void
     */
    public function testRemoveListForMemberNotFoundException(): void
    {
        $this->delete(\sprintf('/mailchimp/lists/%s/members/%s', 'invalid-list-id', 'invalid-member-id'));

        $this->assertListNotFoundResponse('invalid-list-id');
    }

    /**
     * @return array
     */
    private function generateList(): array
    {
        $this->post('/mailchimp/lists', static::$listData);
        $listContent = \json_decode($this->response->getContent(), true);
        $this->createdListIds[] = $listContent['mail_chimp_id'];

        return $listContent;
    }
}