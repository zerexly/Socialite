<?php
namespace api\modules\v1\models;
use Yii;
use api\modules\v1\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use api\modules\v1\models\BlockedUser;

class UserSearch extends User
{
    
    public $searchText;
    public $searchFrom;
    public $isExactMatch;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username','email','searchText'], 'string'],
            [['phone','isExactMatch','searchFrom'], 'integer'],
            [['searchText','isExactMatch','searchFrom'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
          return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    
    /**
     * find a frind     */

    public function searchFindFriend($params)
    {
        $userId= Yii::$app->user->identity->id;
        $modleBlockedUser = new BlockedUser();
        $userIdsBlockedMe = $modleBlockedUser->getUserIdsWhomeBlockMe($userId);
        
        
        $model = new User();
        $this->load($params,'');

        
        $query = $model->find()
        //->select(['user.id','user.name','user.username','user.email','user.description','user.phone','user.image'])
        ->select(['user.id','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude'])
         ->where(['user.role'=>User::ROLE_CUSTOMER])
         ->andwhere(['user.status'=>User::STATUS_ACTIVE])
         ->andwhere(['<>','user.id',$userId])
         ->andWhere(['NOT',['user.id'=>$userIdsBlockedMe]]);
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        
      //  $this->setAttributes($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
       // if($this->searchFrom){
        $searchFrom = $this->searchFrom;
        if($this->isExactMatch){

            if($searchFrom ==1){ //username
                
                $query->andFilterWhere(['username' => $this->searchText]);
            }elseif($searchFrom ==2){ // email
                $query->andFilterWhere(['email'=> $this->searchText]);
            }elseif($searchFrom ==3){ //phone
                $query->andFilterWhere(['phone'=>$this->searchText]);
            }else{

                
                    $query->andFilterWhere(['or',
                    [
                        'username' => $this->searchText,
                    ],
                    [
                        'email' => $this->searchText,
                    ],
                    [
                        'phone' => $this->searchText,
                    ]
                ]);
               
            }


        }else{
            
            if($searchFrom ==1){ //username
                $query->andFilterWhere(['like', 'username', $this->searchText]);
            }elseif($searchFrom ==2){ // email
                $query->andFilterWhere(['like', 'email', $this->searchText]);
            }elseif($searchFrom ==3){ //phone
                $query->andFilterWhere(['like', 'phone', $this->searchText]);
            }else{
                $query->andFilterWhere(['or',
                    ['like', 'username', $this->searchText],
                    ['like', 'email', $this->searchText],
                    ['like', 'phone', $this->searchText],
                    
                ]);
            }

        }




        return $dataProvider;
    }

    public function searchFindFriend_old($params)
    {
       
        $model = new User();
        $this->load($params,'');

        
        $query = $model->find()
        //->select(['user.id','user.name','user.username','user.email','user.description','user.phone','user.image'])
        ->select(['user.id','user.username','user.email','user.bio','user.description','user.image','user.is_verified','user.country_code','user.phone','user.country','user.city','user.sex','user.dob','user.is_chat_user_online','user.chat_last_time_online','user.location','user.latitude','user.longitude'])
         ->where(['user.role'=>User::ROLE_CUSTOMER])
         ->andwhere(['user.status'=>User::STATUS_ACTIVE]);
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        
      //  $this->setAttributes($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
         $query->andFilterWhere([
            //'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,

        ]);
        $query->andFilterWhere(['like', 'username', $this->username]);
        //$query->andFilterWhere(['like', 'artist', $this->name]);

        /*$query->andFilterWhere(['or',
            ['like', 'name', $this->name],
            ['like', 'artist', $this->name]]
        );*/

        return $dataProvider;
    }
    
    
}
