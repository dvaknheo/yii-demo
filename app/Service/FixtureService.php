<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;
use MY\Base\Helper\ModelHelper as M;

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Entity\User;
use Cycle\ORM\Transaction;

use Faker\Factory;
use Faker\Generator;

use Yiisoft\Security\PasswordHasher;
use Yiisoft\Security\Random;


class FixtureService extends BaseService
{
    private Generator $faker;
    /** @var User[] */
    private array $users = [];
    /** @var Tag[] */
    private array $tags = [];

    
    public function run($count)
    {
        $this->faker = Factory::create();
        $this->addUsers($count);
        $this->addTags($count); //=> create
        $this->addPosts($count);

        $this->saveEntities();
    }
    private function saveEntities(): void
    {
        $transaction = new Transaction($this->getORM());
        foreach ($this->users as $user) {
            $transaction->persist($user);
        }
        $transaction->run();
    }

    private function addUsers(int $count): void
    {
        for ($i = 0; $i <= $count; ++$i) {
            $login = $this->faker->firstName . rand(0, 9999);
            $user=UserModel::create($login,$login);
            $this->users[] = $user;
        }
    }

    private function addTags(int $count): void
    {
        $tagWords = [];
        for ($i = 0, $fails = 0; $i <= $count; ++$i) {
            $word = $this->faker->word();
            if (in_array($word, $tagWords, true)) {
                --$i;
                ++$fails;
                if ($fails >= $count) {
                    break;
                }
                continue;
            }
            $tagWords[] = $word;
        }
        ////
        foreach ($tagWords as $word) {
            $tag = $this->getOrCreateTag($word);
            $this->tags[] = $tag;
        }
    }

    private function addPosts(int $count): void
    {
        if (count($this->users) === 0) {
            throw new \Exception('No users');
        }
        for ($i = 0; $i <= $count; ++$i) {
            /** @var User $postUser */
            $user_id = $this->users[array_rand($this->users)];
            //
            $post_id=$this->addPost($user_id);
            if(!$post_id){
                
            }
            $postTags = (array)array_rand($this->tags, rand(1, count($this->tags)));
            foreach ($postTags as $tagId) {
                $this->addPostTag($post_id,$tag_id);
            }
            $commentsCount = rand(0, $count);
            for ($j = 0; $j <= $commentsCount; ++$j) {
                $comment_user_id = $this->users[array_rand($this->users)];
                $this->addComment($comment_user_id,$post_id);
            }
        }
    }
        
    protected function getOrCreateTag($word)
    {
        $sql="select id from tag where label=?";
        $id=M::DB()->fetchColmn($sql,$word);
        if(!empty($id)){
            return $id;
        }
        $created_at = (new \DateTimeImmutable()).format('Y-m-d H:i:s');
        $sql="insert into tag (`label`, `created_at`) VALUES (?,?)";
        M::DB()->execute($sql,$word,$created_at);
        
        return M::DB()->lastInsertId();
    }

    protected function addPost($user_id)
    {
        $title=$this->faker->text(64);
        $content=$this->faker->realText(rand(1000, 4000));

        $public = (rand(0, 3) > 0)?true:false;
        $published_at = $public?(new \DateTimeImmutable(date('r', rand(time(), strtotime('-2 years'))))).format('Y-m-d H:i:s'):null;
        $created_at = (new \DateTimeImmutable()).format('Y-m-d H:i:s');
        $updated_at = (new \DateTimeImmutable()).format('Y-m-d H:i:s');
        $data=[
            'slug'=>$slug,
            'title'=>$title,
            'content'=>$content,
            'created_at'=>$created_at,
            'updated_at'=>$updated_at,
            'published_at'=>$published_at,
            'deleted_at'=>null,
            'user_id'=>$user_id,
        ];
        
        M:DB()->insertData('post',$data);
        
        return M::DB()->lastInsertId();
    }
    protected function addPostTag($post_id,$tag_id)
    {
        $sql="INSERT INTO `post_tag` (`post_id`, `tag_id`) VALUES (?, ?)";
        M::DB()->execute($sql,$post_id,$tag_id);
        return M::DB()->lastInsertId();
    }
    protected function addComment($user_id,$post_id)
    {
        $content= $this->faker->realText(rand(100, 500));
        $public = (rand(0, 3) > 0)?true:false;
        $published_at = $public?(new \DateTimeImmutable(date('r', rand(time(), strtotime('-1 years'))))).format('Y-m-d H:i:s'):null;
        $created_at = (new \DateTimeImmutable()).format('Y-m-d H:i:s');
        $updated_at = (new \DateTimeImmutable()).format('Y-m-d H:i:s');
        $data=[
            'public'=>$public,
            'content'=>$content,
            
            'created_at'=>$created_at,
            'updated_at'=>$updated_at,
            'published_at'=>$published_at,
            'deleted_at'=>null,
            'post_id'=>$post_id,
            
            'user_id'=>$user_id,
        ];
        
        M:DB()->insertData('comment',$data);
        
        return M::DB()->lastInsertId();
    }
}

/*
2020-04-24 12:39:37.247600 [info][application] SELECT `tag`.`id` AS `c0`, `tag`.`label` AS `c1`, `tag`.`created_at` AS `c2`
FROM `tag` AS `tag`
WHERE `tag`.`label` = 'dolore' 
LIMIT 1
2020-04-24 12:39:37.259600 [info][application] SELECT `tag`.`id` AS `c0`, `tag`.`label` AS `c1`, `tag`.`created_at` AS `c2`
FROM `tag` AS `tag`
WHERE `tag`.`label` = 'minima' 
LIMIT 1
2020-04-24 12:39:37.645100 [info][application] Begin transaction
2020-04-24 12:39:37.645700 [info][application] INSERT INTO `user` (`token`, `login`, `password_hash`, `created_at`, `updated_at`) VALUES ('tt_ECK0NgBlBvM0KIsRBdj3EYIv6zZHOEjh8gY8AeKePx1dXBnikWUxIwpCn6Y3SJ1hWquYBCNyHWT38GTIdu2BwwWeNX-mA4fJ6gFuLj7iFpnDn66KUvEuh34R6_jhA', 'Nils681', '$2y$13$TSWk.2vOvEQzhjUPknHy.uo420PyWX4Had84Viu81Gac3QnHYou0e', '2020-04-24T20:39:37+08:00', '2020-04-24T20:39:37+08:00')
2020-04-24 12:39:37.645700 [debug][application] Insert ID: 31
2020-04-24 12:39:37.759000 [info][application] UPDATE `user`
SET `created_at` = '2020-04-24T20:39:33+08:00', `updated_at` = '2020-04-24T20:39:37+08:00'
WHERE `id` = 31 
2020-04-24 12:39:37.760100 [info][application] INSERT INTO `post` (`slug`, `title`, `public`, `content`, `created_at`, `updated_at`, `published_at`, `deleted_at`, `user_id`) VALUES ('Sj0vaHiJ62fxiQFoU_zLUdOfEC1ND2Txrue4hWGfMqyQs2REWZPHkyrCZAbBQgj-ACU-ePcd8SUVRHJ5sTipYZmyJsJ9pxNzxOjdWSa84Z0fvOhXYhrWEkw-pszzz2fU', 'Deserunt et corporis unde nihil aut quos quas.', TRUE, 'March Hare. \'Sixteenth,\' added the Queen. \'Never!\' said the March Hare was said to the Dormouse, without considering at all for any of them. \'I\'m sure I\'m not particular as to size,\' Alice hastily replied; \'at least--at least I know is, it would be offended again. \'Mine is a long time with great curiosity, and this Alice thought decidedly uncivil. \'But perhaps he can\'t help that,\' said the Mouse. \'--I proceed. "Edwin and Morcar, the earls of Mercia and Northumbria--"\' \'Ugh!\' said the Footman, \'and that for two reasons. First, because I\'m on the hearth and grinning from ear to ear. \'Please would you like the look of the other paw, \'lives a Hatter: and in despair she put her hand on the song, perhahey\'re dreadfully fond of beheading people here; the great concert given by the soldiers, who of course had to be trampled under its feet, ran round the neck of the way--\' \'THAT generally takes some time,\' interrupted the Hatter: \'it\'s very rude.\' The Hatter looked at it again: but he now hastily began again, using the ink, that was said, and went on growing, and, as the White Rabbit cried out, \'Silence in the trial done,\' she thought, \'till its ears have come, or at least one of these cakes,\' she thought, and it was good manners for her to begin.\' For, you see, as she ran. \'How surprised he\'ll be when he sneezes; For he can thoroughly enjoy The pepper when he.', '2020-01-19T22:06:50+08:00', NULL, 31, ?, ?)
2020-04-24 12:39:37.760100 [debug][application] Insert ID: 20
2020-04-24 12:39:37.827200 [info][application] INSERT INTO `tag` (`label`, `created_at`) VALUES ('dolore', '2020-04-24T20:39:37+08:00')
2020-04-24 12:39:37.827200 [debug][application] Insert ID: 20
2020-04-24 12:39:37.827600 [info][application] INSERT INTO `post_tag` (`post_id`, `tag_id`) VALUES (20, 20)
2020-04-24 12:39:37.827600 [debug][application] Insert ID: 92
2020-04-24 12:39:37.827800 [info][application] INSERT INTO `tag` (`label`, `created_at`) VALUES ('minima', '2020-04-24T20:39:37+08:00')
2020-04-24 12:39:37.827900 [debug][application] Insert ID: 21
2020-04-24 12:39:37.892100 [info][application] INSERT INTO `post_tag` (`post_id`, `tag_id`) VALUES (20, 21)
2020-04-24 12:39:37.892100 [debug][application] Insert ID: 93
2020-04-24 12:39:37.892500 [info][application] INSERT INTO `comment` (`public`, `content`, `created_at`, `updated_at`, `published_at`, `deleted_at`, `user_id`, `post_id`) VALUES (TRUE, 'Alice. \'And where HAVE my shoulders got to'2020-04-24T20:39:37+08:00' And oh, I wish I could say if I know is, something comes at me like that!\' By this time it vanished quite slowly, beginning with the dream of Wonderland of long ago: and how she was now the right size for ten minutes together!\' \'Can\'t remember WHAT.', '2020-04-24T20:39:37+08:00', '2019-11-10T05:31:11+08:00', NULL, 31, 20, ?)
2020-04-24 12:39:37.892500 [debug][application] Insert ID: 92
2020-04-24 12:39:37.892800 [info][application] INSERT INTO `user` (`token`, `login`, `password_hash`, `created_at`, `updated_at`) VALUES ('XSDSxn0B-RtVCkPDNY1tlKBqsUZH7S-NnecGQcWsaxkmVa3QG_EXfWVQ92UFGhX3vOdJ7Ie_a9YP0n0cwhXOZrdGcAdm22mY__IWcUbFt6fnnK2zuO5KdLY6YEGl49xl', 'Tristin7761', '$2y$13$3jA3G0essBC3tP.qeSu.zOATywyHMqkzp4TkBTv5JiXE.vtWotEaq', '2020-04-24T20:39:37+08:00', '2020-04-24T20:39:37+08:00')
2020-04-24 12:39:37.892800 [debug][application] Insert ID: 32
2020-04-24 12:39:37.893200 [info][application] UPDATE `user`
SET `created_at` = '2020-04-24T20:39:34+08:00', `updated_at` = '2020-04-24T20:39:37+08:00'
WHERE `id` = 32 
2020-04-24 12:39:37.893400 [info][application] UPDATE `post`
SET `created_at` = '2020-04-24T20:39:37+08:00', `updated_at` = '2020-04-24T20:39:37+08:00'
WHERE `id` = 20 
2020-04-24 12:39:37.893800 [info][application] INSERT INTO `comment` (`public`, `content`, `created_at`, `updated_at`, `published_at`, `deleted_at`, `user_id`, `post_id`) VALUES (TRUE, 'Alice; \'it\'s laid for a rabbit! I suppose Dinah\'ll be sending me on messages next!\' And she thought it had no idea what you\'re doing!\' cried Alice, jumping up in her head, and she at once in the window, she suddenly spread out her hand on the bank, and of having the sentence first!\' \'Hold your.', '2020-04-24T20:39:37+08:00', '2020-04-24T20:39:37+08:00', '2019-05-09T04:06:26+08:00', NULL, 32, 20)
2020-04-24 12:39:37.893800 [debug][application] Insert ID: 93
2020-04-24 12:39:37.894100 [info][application] INSERT INTO `post` (`slug`, `title`, `public`, `content`, `created_at`, `updated_at`, `published_at`, `deleted_at`, `user_id`) VALUES ('aEUuy0HkblOI-VaP6MRvkh_dg2QUT-I7mHatQzorwjAstr_k3U1UTRTm8QOCvSbs-IfatjCZC-Hv0iVehD0qX60SBqWLnPhbwB-TWAWSXH0GBQtHSqW9vFQQ5HO5AfJL', 'Fuga ducimus itaque et officia.', TRUE, 'Queen was close behind it when she noticed that they had to kneel down on one knee. \'I\'m a poor man,\' the Hatter said, turning to Alice for some time without interrupting it. \'They must go and take it away!\' There was nothing else to say but \'It belongs to the Gryphon. \'Turn a somersault in the morning, just time to begin at HIS time of life. The King\'s argument was, that anything that had fluttered down from the time they had a wink of sleep these three little sisters,\' the Dormouse go on till you come and join the dance'2020-04-24T20:39:37+08:00' Will you, won\'t you join the dance'2020-04-24T20:39:37+08:00' "You can really have no idea what you\'re talking about,\' said Alice. \'Anything you like,\' said the Cat. \'I\'d nearly forgotten that I\'ve got to do,\' said Alice hastily; \'but I\'m not used to say \'creatures,\' you see, as they all spoke at once, and ran till she heard it before,\' said the Mouse, who was peeping anxiously into its face to see what the next moment she appeared; but she felt sure she would feel very queer indeed:-- \'\'Tis the voice of the Queen had never forgotten that, if you drink much from a Caterpillar The.', '2019-02-25T08:54:50+08:00', NULL, 32, ?, ?)
2020-04-24 12:39:37.894100 [debug][application] Insert ID: 21
2020-04-24 12:39:37.894400 [info][application] UPDATE `tag`
SET `created_at` = '2020-04-24T20:39:37+08:00'
WHERE `id` = 21 
2020-04-24 12:39:37.894700 [info][application] INSERT INTO `post_tag` (`post_id`, `tag_id`) VALUES (21, 21)
2020-04-24 12:39:37.894700 [debug][application] Insert ID: 94
2020-04-24 12:39:37.894900 [info][application] INSERT INTO `comment` (`public`, `content`, `created_at`, `updated_at`, `published_at`, `deleted_at`, `user_id`, `post_id`) VALUES (TRUE, 'FIT you,\' said Alice, looking down at them, and was beating her violently with its legs hanging down, but generally, just as well look and see what was.', '2020-04-24T20:39:37+08:00', '2020-04-24T20:39:37+08:00', '2019-11-01T13:07:29+08:00', NULL, 32, 21)
2020-04-24 12:39:37.895000 [debug][application] Insert ID: 94
2020-04-24 12:39:37.895200 [info][application] Commit transaction

//*/
