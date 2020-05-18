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
    public function testCreateListSuccessfully(): void
    {
        $list = $this->createList(static::$listData);
        $member = $this->createMember(static::$memberData);

        $this->get(\sprintf('/mailchimp/lists/%s/members/%s', $list->getId(), $member->getId()));
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (static::$memberData as $key => $value) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals($value, $content[$key]);
        }
    }
}