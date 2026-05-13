<?php
/**
 * 16 型人格資料。
 * key 為小寫 4 字母代碼，對應 enfj.php 等檔名。
 * functions: 榮格八維由 1 位到 8 位
 * desc: 人格介紹文字
 * prev/next: 上一個 / 下一個型號 (小寫)
 */
return [
    'intj' => [
        'functions' => ['Ni', 'Te', 'Fi', 'Se', 'Ne', 'Ti', 'Fe', 'Si'],
        'desc'      => '卷王，偏執狂，執行力極高，為達目的不擇手段，PLAN-A/B/C，有嚴重的秩序敏感，面具人2號。',
        'prev'      => 'esfp',
        'next'      => 'intp',
    ],
    'intp' => [
        'functions' => ['Ti', 'Ne', 'Si', 'Fe', 'Te', 'Ni', 'Se', 'Fi'],
        'desc'      => '冷酷無情，智商高，情商低，數學好，能躺著絕不站著，自制力為0。',
        'prev'      => 'intj',
        'next'      => 'entj',
    ],
    'entj' => [
        'functions' => ['Te', 'Ni', 'Se', 'Fi', 'Ti', 'Ne', 'Si', 'Fe'],
        'desc'      => '大老闆，愛指揮所有人，大姐大/大哥大，效率與利益至上，大忙人。',
        'prev'      => 'intp',
        'next'      => 'entp',
    ],
    'entp' => [
        'functions' => ['Ne', 'Ti', 'Fe', 'Si', 'Ni', 'Te', 'Fi', 'Se'],
        'desc'      => '杠精，什麼事都喜歡辯論，臉皮厚，愛冒犯人，喜歡捉弄別人，愛開地獄玩笑，沒邊界感。',
        'prev'      => 'entj',
        'next'      => 'infj',
    ],
    'infj' => [
        'functions' => ['Ni', 'Fe', 'Ti', 'Se', 'Ne', 'Fi', 'Te', 'Si'],
        'desc'      => '不真誠，絕情，面具狂魔，白切黑，討厭與人相處，卻又表現慷慨。',
        'prev'      => 'entp',
        'next'      => 'infp',
    ],
    'infp' => [
        'functions' => ['Fi', 'Ne', 'Si', 'Te', 'Fe', 'Ni', 'Se', 'Ti'],
        'desc'      => '恁蝶，愛擺爛，拖延症，想法幼稚，愛哭，特別多愁善感，內耗，脆弱又敏感，沒有解決問題的能力。',
        'prev'      => 'infj',
        'next'      => 'enfj',
    ],
    'enfj' => [
        'functions' => ['Fe', 'Ni', 'Se', 'Ti', 'Fi', 'Ne', 'Si', 'Te'],
        'desc'      => '道德綁架，雙標，和稀泥大師，開口即是：榮譽、集體、團結。',
        'prev'      => 'infp',
        'next'      => 'enfp',
    ],
    'enfp' => [
        'functions' => ['Ne', 'Fi', 'Te', 'Si', 'Ni', 'Fe', 'Ti', 'Se'],
        'desc'      => '以自我為中心，容易下頭變心，快樂小狗，渴望被偏愛，三分鐘熱度，三分鐘熱度，缺乏實踐和務實精神，完全安靜不下來。',
        'prev'      => 'enfj',
        'next'      => 'istj',
    ],
    'istj' => [
        'functions' => ['Si', 'Te', 'Fi', 'Ne', 'Se', 'Ti', 'Fe', 'Ni'],
        'desc'      => '死犟種，人機，不懂變通，只會埋頭苦幹，天選打工人。',
        'prev'      => 'enfp',
        'next'      => 'isfj',
    ],
    'isfj' => [
        'functions' => ['Si', 'Fe', 'Ti', 'Ne', 'Se', 'Fi', 'Te', 'Ni'],
        'desc'      => '老好人，多愁善感，容易自卑，自我懷疑，犧牲自我。',
        'prev'      => 'istj',
        'next'      => 'estj',
    ],
    'estj' => [
        'functions' => ['Te', 'Si', 'Ne', 'Fi', 'Ti', 'Se', 'Ni', 'Fe'],
        'desc'      => '中式家長，喜歡命令別人，態度強硬，唯命是從，利己主義者，沒人生理想，說一不二。',
        'prev'      => 'isfj',
        'next'      => 'esfj',
    ],
    'esfj' => [
        'functions' => ['Fe', 'Si', 'Ne', 'Ti', 'Fi', 'Se', 'Ni', 'Te'],
        'desc'      => '愛多管閒事，喜歡閒聊，婆婆媽媽，道德綁架。',
        'prev'      => 'estj',
        'next'      => 'istp',
    ],
    'istp' => [
        'functions' => ['Ti', 'Se', 'Ni', 'Fe', 'Te', 'Si', 'Ne', 'Fi'],
        'desc'      => '社會哥，人狠話不多，情商低，不理人，厭蠢症，看誰不順眼就開罵，感情上遲鈍，聽不懂任何潛台詞。',
        'prev'      => 'esfj',
        'next'      => 'isfp',
    ],
    'isfp' => [
        'functions' => ['Fi', 'Se', 'Ni', 'Te', 'Fe', 'Si', 'Ne', 'Ti'],
        'desc'      => '文藝青年，幼稚又愛幻想，只顧自己，擺爛，拖延症，不接受批評，對任何事情都不在乎，一定會畫畫。',
        'prev'      => 'istp',
        'next'      => 'estp',
    ],
    'estp' => [
        'functions' => ['Se', 'Ti', 'Fe', 'Ni', 'Si', 'Te', 'Fi', 'Ne'],
        'desc'      => '喜歡開黃色玩笑，自來熟，沒邊界感，及時行樂，莽撞，沒有責任。',
        'prev'      => 'isfp',
        'next'      => 'esfp',
    ],
    'esfp' => [
        'functions' => ['Se', 'Fi', 'Te', 'Ni', 'Si', 'Fe', 'Ti', 'Ne'],
        'desc'      => '浮誇，愛慕虛榮，表演型人格，社交恐怖分子，娛樂至上，不顧後果。',
        'prev'      => 'estp',
        'next'      => 'intj',
    ],
];
