<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;
use MY\Base\Helper\ModelHelper as M;
use MY\Model\UserModel;

use Faker\Factory;
use Faker\Generator;

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
        $this->addTags($count);
        $this->addPosts($count);

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
        for ($i = 0; $i < $count; ++$i) {
            /** @var User $postUser */
            $user_id = $this->users[array_rand($this->users)];
            //
            $post_id=$this->addPost($user_id);
            if(!$post_id){
                var_dump($post_id);
                continue;
            }
            $postTags = (array)array_rand($this->tags, rand(1, count($this->tags)));
            foreach ($postTags as $key) {
                $tag_id=$this->tags[$key];
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
        $id=M::DB()->fetchColumn($sql,$word);
        if(!empty($id)){
            return $id;
        }
        $created_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $sql="insert into tag (`label`, `created_at`) VALUES (?,?)";
        M::DB()->execute($sql,$word,$created_at);
        
        return M::DB()->lastInsertId();
    }

    protected function addPost($user_id)
    {
        $title=$this->faker->text(64);
        $slug=staic::RandomString(128);
        $content=$this->faker->realText(rand(1000, 4000));

        $public = (rand(0, 3) > 0)?true:false;
        $published_at = $public?(new \DateTimeImmutable(date('r', rand(time(), strtotime('-2 years')))))->format('Y-m-d H:i:s'):null;
        $created_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $updated_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $data=[
            'slug'=>$slug,
            'title'=>$title,
            'content'=>$content,
            'created_at'=>$created_at,
            'updated_at'=>$updated_at,
            'public'=>$public,
            'published_at'=>$published_at,
            'deleted_at'=>null,
            'user_id'=>$user_id,
        ];
        
        M::DB()->insertData('post',$data);
        
        return M::DB()->lastInsertId();
    }
    protected function addPostTag($post_id,$tag_id)
    {
        $sql="INSERT INTO `post_tag` (`post_id`, `tag_id`) VALUES (?, ?)";
        M::DB()->execute($sql,(int)$post_id,$tag_id);
        return M::DB()->lastInsertId();
    }
    protected function addComment($user_id,$post_id)
    {
        $content= $this->faker->realText(rand(100, 500));
        $public = (rand(0, 3) > 0)?true:false;
        $published_at = $public?(new \DateTimeImmutable(date('r', rand(time(), strtotime('-1 years')))))->format('Y-m-d H:i:s'):null;
        $created_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $updated_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $data=[
            'public'=>$public,
            'content'=>$content,
            
            'created_at'=>$created_at,
            'updated_at'=>$updated_at,
            'published_at'=>$published_at,
            'public'=>$public,
            'deleted_at'=>null,
            'post_id'=>$post_id,
            
            'user_id'=>$user_id,
        ];
        
        M::DB()->insertData('comment',$data);
        
        return M::DB()->lastInsertId();
    }
    protected static function RandomString(int $length = 32): string
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('First parameter ($length) must be greater than 0');
        }

        $bytes = random_bytes($length);
        return substr(StringHelper::base64UrlEncode($bytes), 0, $length);
    }
}
