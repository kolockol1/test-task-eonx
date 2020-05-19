<?php
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

use App\Database\Entities\MailChimp\MailChimpMember;
use Mailchimp\Mailchimp;

abstract class MemberTestCase extends ListTestCase
{
    protected $createdMembers = [];

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

    /**
     * Create MailChimp list into database.
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
}