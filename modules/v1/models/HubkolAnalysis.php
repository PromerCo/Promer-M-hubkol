<?php
namespace mhubkol\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hubkol_analysis".
 *
 * @property integer $id
 * @property integer $pt
 * @property integer $p_author_incr_played
 * @property integer $p_author_incr_fans
 * @property integer $p_author_fans
 * @property integer $p_author_incr_comments
 * @property integer $p_author_incr_thumbs
 * @property integer $p_author_incr_booms
 * @property integer $p_author_incr_shares
 * @property string $create_time
 * @property string $update_time
 */
class HubkolAnalysis extends \backend\models\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hubkol_analysis';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pt', 'p_author_incr_played', 'p_author_incr_fans', 'p_author_fans', 'p_author_incr_comments', 'p_author_incr_thumbs', 'p_author_incr_booms', 'p_author_incr_shares'], 'integer'],
            [['create_time', 'update_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pt' => 'Pt',
            'p_author_incr_played' => 'P Author Incr Played',
            'p_author_incr_fans' => 'P Author Incr Fans',
            'p_author_fans' => 'P Author Fans',
            'p_author_incr_comments' => 'P Author Incr Comments',
            'p_author_incr_thumbs' => 'P Author Incr Thumbs',
            'p_author_incr_booms' => 'P Author Incr Booms',
            'p_author_incr_shares' => 'P Author Incr Shares',
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
		'pt' => array(
                        'name' => 'pt',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '',
//                         'dbType' => "int(11)",
                        'defaultValue' => '',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '11',
                        'scale' => '',
                        'size' => '11',
                        'type' => 'integer',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('pt'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'p_author_incr_played' => array(
                        'name' => 'p_author_incr_played',
                        'allowNull' => true,
//                         'autoIncrement' => false,
//                         'comment' => '',
//                         'dbType' => "int(11)",
                        'defaultValue' => '0',
                        'enumValues' => null,
                        'isPrimaryKey' => false,
                        'phpType' => 'integer',
                        'precision' => '11',
                        'scale' => '',
                        'size' => '11',
                        'type' => 'integer',
                        'unsigned' => false,
                        'label'=>$this->getAttributeLabel('p_author_incr_played'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'p_author_incr_fans' => array(
                        'name' => 'p_author_incr_fans',
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
                        'label'=>$this->getAttributeLabel('p_author_incr_fans'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'p_author_fans' => array(
                        'name' => 'p_author_fans',
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
                        'label'=>$this->getAttributeLabel('p_author_fans'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'p_author_incr_comments' => array(
                        'name' => 'p_author_incr_comments',
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
                        'label'=>$this->getAttributeLabel('p_author_incr_comments'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'p_author_incr_thumbs' => array(
                        'name' => 'p_author_incr_thumbs',
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
                        'label'=>$this->getAttributeLabel('p_author_incr_thumbs'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'p_author_incr_booms' => array(
                        'name' => 'p_author_incr_booms',
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
                        'label'=>$this->getAttributeLabel('p_author_incr_booms'),
                        'inputType' => 'text',
                        'isEdit' => true,
                        'isSearch' => false,
                        'isDisplay' => true,
                        'isSort' => true,
//                         'udc'=>'',
                    ),
		'p_author_incr_shares' => array(
                        'name' => 'p_author_incr_shares',
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
                        'label'=>$this->getAttributeLabel('p_author_incr_shares'),
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
