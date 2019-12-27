<?php
namespace mhubkol\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hubkol_dtdformation".
 *
 * @property integer $id
 * @property string $platform
 * @property integer $author_id
 * @property integer $episode_avg_played
 * @property integer $episode_avg_thumbs
 * @property integer $episode_avg_comments
 * @property integer $episode_avg_shares
 * @property integer $fans_incr
 * @property integer $videos_incr
 * @property integer $videos_total
 * @property double $comment_thumb_ratio
 * @property string $create_time
 * @property string $update_time
 */
class HubkolDtdformation extends \backend\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hubkol_dtdformation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id', 'episode_avg_played', 'episode_avg_thumbs', 'episode_avg_comments', 'episode_avg_shares', 'fans_incr', 'videos_incr', 'videos_total'], 'integer'],
            [['comment_thumb_ratio'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['platform'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'platform' => 'Platform',
            'author_id' => 'Author ID',
            'episode_avg_played' => 'Episode Avg Played',
            'episode_avg_thumbs' => 'Episode Avg Thumbs',
            'episode_avg_comments' => 'Episode Avg Comments',
            'episode_avg_shares' => 'Episode Avg Shares',
            'fans_incr' => 'Fans Incr',
            'videos_incr' => 'Videos Incr',
            'videos_total' => 'Videos Total',
            'comment_thumb_ratio' => 'Comment Thumb Ratio',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

  /**
     * 返回数据库字段信息，仅在生成CRUD时使用，如不需要生成CRUD，请注释或删除该getTableColumnInfo()代码
     * COLUMN_COMMENT可用key如下:
     * label - 显示的label
     * inputType 控件类型, 暂时只支持text,hidden  // select,checkbox,radio,file,password,
     * isEdit   是否允许编辑，如果允许编辑将在添加和修改时输入
     * isSearch 是否允许搜索
     * isDisplay 是否在列表中显示
     * isOrder 是否排序
     * udc - udc code，inputtype为select,checkbox,radio三个值时用到。
     * 特别字段：
     * id：主键。必须含有主键，统一都是id
     * create_date: 创建时间。生成的代码自动赋值
     * update_date: 修改时间。生成的代码自动赋值
     */
    public function getTableColumnInfo(){
        return array(
        'id' => array(
                        'name' => 'id',
                        'allowNull' => false,
//                         'autoIncrement' => true,
//                         'comment' => '',
//                         'dbType' => "bigint(20)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => true,
                        'phpType' => 'integer',
                        'precision' => '20',
                        'scale' => '',
                        'size' => '20',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('id'),
                        'inputType' => 'hidden',
                        'isEdit' => true,
                        'isSearch' => true,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'platform' => array(
                        'name' => 'platform',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '',
//                         'dbType' => "varchar(32)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '32',
                        'scale' => '',
                        'size' => '32',
                        'type' => 'string',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('platform'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'author_id' => array(
                        'name' => 'author_id',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '',
//                         'dbType' => "bigint(11)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '11',
                        'scale' => '',
                        'size' => '11',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('author_id'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'episode_avg_played' => array(
                        'name' => 'episode_avg_played',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '平均播放集',
//                         'dbType' => "bigint(10)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '10',
                        'scale' => '',
                        'size' => '10',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('episode_avg_played'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'episode_avg_thumbs' => array(
                        'name' => 'episode_avg_thumbs',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '平均点赞量',
//                         'dbType' => "bigint(10)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '10',
                        'scale' => '',
                        'size' => '10',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('episode_avg_thumbs'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'episode_avg_comments' => array(
                        'name' => 'episode_avg_comments',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '平均评论量',
//                         'dbType' => "bigint(10)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '10',
                        'scale' => '',
                        'size' => '10',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('episode_avg_comments'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'episode_avg_shares' => array(
                        'name' => 'episode_avg_shares',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '平均分享量',
//                         'dbType' => "bigint(10)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '10',
                        'scale' => '',
                        'size' => '10',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('episode_avg_shares'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'fans_incr' => array(
                        'name' => 'fans_incr',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '粉丝增长量',
//                         'dbType' => "bigint(10)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '10',
                        'scale' => '',
                        'size' => '10',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('fans_incr'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'videos_incr' => array(
                        'name' => 'videos_incr',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '视频增长量',
//                         'dbType' => "bigint(10)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '10',
                        'scale' => '',
                        'size' => '10',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('videos_incr'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'videos_total' => array(
                        'name' => 'videos_total',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '视频总播放量',
//                         'dbType' => "bigint(10)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '10',
                        'scale' => '',
                        'size' => '10',
                        'type' => 'bigint',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('videos_total'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'comment_thumb_ratio' => array(
                        'name' => 'comment_thumb_ratio',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '评论率',
//                         'dbType' => "float(10,5)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'double',
                        'precision' => '10',
                        'scale' => '5',
                        'size' => '10',
                        'type' => 'float',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('comment_thumb_ratio'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'create_time' => array(
                        'name' => 'create_time',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '',
//                         'dbType' => "timestamp",
                        'defaultValue' => 'CURRENT_TIMESTAMP',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '',
                        'scale' => '',
                        'size' => '',
                        'type' => 'timestamp',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('create_time'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'update_time' => array(
                        'name' => 'update_time',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '',
//                         'dbType' => "timestamp",
                        'defaultValue' => 'CURRENT_TIMESTAMP',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'string',
                        'precision' => '',
                        'scale' => '',
                        'size' => '',
                        'type' => 'timestamp',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('update_time'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		        );
        
    }
 
}
