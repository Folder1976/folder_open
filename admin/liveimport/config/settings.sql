TRUNCATE TABLE <?php echo t('parsemx_donors') ?>;
INSERT INTO parsemx_donors SET donor_id='1', host='import', autorun='', missing='', total_size='285712420', check_url='', check_lurl='', dtype='file', advanced='', auto_update='', updated='';
INSERT INTO parsemx_donors SET donor_id='5', host='tehnopostavka.com.ua', autorun='', missing='', total_size='0', check_url='http://tehnopostavka.com.ua/televizory/televizory-hitachi/televizor-hitachi-32hb1t65', check_lurl='', dtype='', advanced='', auto_update='', updated='';

TRUNCATE TABLE <?php echo t('parsemx_ins') ?>;
INSERT INTO parsemx_ins SET donor_id='1', title='Default', url='', categories='', manufacturer='0', price='X', status='1';
INSERT INTO parsemx_ins SET donor_id='5', title='Default', url='', categories='600480936', manufacturer='0', price='X', status='0';
