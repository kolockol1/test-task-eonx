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
        // create list
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
        // create list
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