<?php namespace App\Models\Accounts;

use App\Models\Comments\Comment;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model {

    const WIKI_OBJECT = 'wo';
    const WIKI_REVISION = 'wr';
    const FORUM_THREAD = 'ft';
    const VAULT_CATEGORY = 'vc';
    const VAULT_ITEM = 'vi';
    const NEWS = 'ns';
    const JOURNAL = 'jn';
    const POLL = 'po';

	protected $table = 'user_notifications';
	protected $fillable = [ 'user_id', 'article_type', 'article_id', 'is_unread', 'is_processed' ];
    public $visible = [ ];

    protected $appends = ['type_description','link'];
    public function getTypeDescriptionAttribute() {
        switch ($this->article_type) {
            case UserSubscription::WIKI_OBJECT: return 'Wiki Comments';
            case UserSubscription::WIKI_REVISION: return 'Wiki Page';
            case UserSubscription::FORUM_THREAD: return 'Forum Thread';
            case UserSubscription::VAULT_CATEGORY: return 'Vault Category';
            case UserSubscription::VAULT_ITEM: return 'Vault Item';
            case UserSubscription::NEWS: return 'News Article';
            case UserSubscription::JOURNAL: return 'Journal';
            case UserSubscription::POLL: return 'Poll';
            default: return 'Unknown';
        }
    }
    public function getLinkAttribute() {
        $id = $this->article_id;
        switch ($this->article_type) {
            case UserSubscription::WIKI_OBJECT:
            case UserSubscription::WIKI_REVISION: return act('wiki', 'view', $id);
            case UserSubscription::FORUM_THREAD: return act('thread', 'view', $id).'?page=last';
            case UserSubscription::VAULT_CATEGORY: return act('vault', 'index').'?cats='.$id;
            case UserSubscription::VAULT_ITEM: return act('vault', 'view', $id);
            case UserSubscription::NEWS: return act('news', 'view', $id);
            case UserSubscription::JOURNAL: return act('journal', 'view', $id);
            case UserSubscription::POLL: return act('poll', 'view', $id);
            default: return 'Unknown';
        }
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Accounts\User');
    }

    public function wiki_revision()
    {
        return $this->belongsTo('App\Models\Wiki\WikiRevision', 'article_id', 'id');
    }

    public function forum_thread()
    {
        return $this->belongsTo('App\Models\Forums\ForumThread', 'article_id', 'id');
    }

    public static function GetTypeFromCommentType($comment_type)
    {
        switch ($comment_type) {
            case Comment::NEWS: return UserNotification::NEWS;
            case Comment::JOURNAL: return UserNotification::JOURNAL;
            case Comment::VAULT: return UserNotification::VAULT_ITEM;
            case Comment::POLL: return UserNotification::POLL;
            case Comment::WIKI: return UserNotification::WIKI_OBJECT;
            default: return null;
        }
    }
}
