<?php
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

use App\Database\Entities\MailChimp\MailChimpMember;
use Mailchimp\Mailchimp;

abstract class MemberTestCase extends ListTestCase
{
    private $createdMembers = [];

    public function tearDown(): void
    {
        /** @var Mailchimp $mailChimp */
        $mailChimp = $this->app->make(Mailchimp::class);

        foreach ($this->createdMembers as $member) {
            // Delete members on MailChimp after test
            $mailChimp->delete(\sprintf('lists/%s/members/%s', $member['listId'], $member['memberId']));
        }

        parent::tearDown();
    }

    /**
     * @return array
     */
    protected function generateList(): array
    {
        $this->post('/mailchimp/lists', static::$listData);
        $listContent = \json_decode($this->response->getContent(), true);
        $this->createdListIds[] = $listContent['mail_chimp_id'];

        return $listContent;
    }

    protected function addCreatedMember(string $listId, string $memberId): void
    {
        $this->createdMembers[] = [
            'listId' => $listId,
            'memberId' => $memberId,
        ];
    }

    protected static $memberData = [
        'email_address' => 'techTask@enjoy.com',
        'email_type' => 'text',
        'status' => 'unsubscribed',
        'language' => 'ru',
        'vip' => false,
        'location' => [
            'latitude' => '55.55',
            'longitude' => '44.44',
        ],
        'ip_signup' => '1.1.1.1',
        'timestamp_signup' => '2020-11-05T08:15:30-05:00',
        'ip_opt' => '2.2.2.2',
        'timestamp_opt' => '2020-11-05T08:15:30-05:00',
        'tags' => [],
    ];

    protected static $notRequiredForMember = [
        'email_type',
        'merge_fields',
        'interests',
        'language',
        'vip',
        'location',
        'marketing_permissions',
        'ip_signup',
        'timestamp_signup',
        'ip_opt',
        'timestamp_opt',
        'tags',
    ];

    /**
     * Create MailChimp member into database.
     *
     * @param array $data
     *
     * @return MailChimpMember
     */
    protected function createMember(array $data): MailChimpMember
    {
        $member = new MailChimpMember($data);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    /**
     * Asserts error response when member not found.
     *
     * @param string $memberId
     *
     * @return void
     */
    protected function assertMemberNotFoundResponse(string $memberId): void
    {
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(404);
        self::assertArrayHasKey('message', $content);
        self::assertEquals(\sprintf('MailChimpMember[%s] not found', $memberId), $content['message']);
    }
}