<?php namespace net\xp_framework\unittest\rdbms;

use unittest\TestCase;
use util\Date;
use rdbms\sybase\SybaseConnection;
use rdbms\mysql\MySQLConnection;
use rdbms\pgsql\PostgreSQLConnection;
use rdbms\sqlite\SQLiteConnection;
use rdbms\criterion\Restrictions;
use rdbms\criterion\Projections;
use net\xp_framework\unittest\rdbms\dataset\Job;

/**
 * TestCase
 *
 * @see  xp://rdbms.criterion.Projections
 */
class ProjectionTest extends TestCase {
  public
    $syconn = null,
    $myconn = null,
    $pgconn = null,
    $sqconn = null,
    $peer   = null;
    
  /**
   * Sets up a Database Object for the test
   *
   */
  public function setUp() {
    $this->syconn= new SybaseConnection(new \rdbms\DSN('sybase://localhost:1999/'));
    $this->myconn= new MySQLConnection(new \rdbms\DSN('mysql://localhost/'));
    $this->pgconn= new PostgreSQLConnection(new \rdbms\DSN('pgsql://localhost/'));
    $this->sqconn= new SQLiteConnection(new \rdbms\DSN('sqlite://tmpdir/tmpdb'));
    $this->peer= Job::getPeer();
  }
  
  /**
   * Helper method that will call toSQL() on the passed criteria and
   * compare the resulting string to the expected string.
   *
   * @param   string mysql
   * @param   string sysql
   * @param   string pgsql
   * @param   string sqlite
   * @param   rdbms.Criteria criteria
   * @throws  unittest.AssertionFailedError
   */
  protected function assertSql($mysql, $sysql, $pgsql, $sqlite, $criteria) {
    $this->assertEquals('mysql: '.$mysql,  'mysql: '.trim($criteria->toSQL($this->myconn, $this->peer), ' '));
    $this->assertEquals('sybase: '.$sysql, 'sybase: '.trim($criteria->toSQL($this->syconn, $this->peer), ' '));
    $this->assertEquals('pgsql: '.$pgsql, 'pgsql: '.trim($criteria->toSQL($this->pgconn, $this->peer), ' '));
    $this->assertEquals('sqlite: '.$sqlite, 'sqlite: '.trim($criteria->toSQL($this->sqconn, $this->peer), ' '));
  }
  
  /**
   * Helper method that will call projection() on the passed criteria and
   * compare the resulting string to the expected string.
   *
   * @param   string mysql
   * @param   string sysql
   * @param   string pgsql
   * @param   string sqlite
   * @param   rdbms.Criteria criteria
   * @throws  unittest.AssertionFailedError
   */
  protected function assertProjection($mysql, $sysql, $pgsql, $sqlite, $criteria) {
    $this->assertEquals('mysql: '.$mysql,  'mysql: '.trim($criteria->projections($this->myconn, $this->peer), ' '));
    $this->assertEquals('sybase: '.$sysql, 'sybase: '.trim($criteria->projections($this->syconn, $this->peer), ' '));
    $this->assertEquals('pgsql: '.$pgsql, 'pgsql: '.trim($criteria->projections($this->pgconn, $this->peer), ' '));
    $this->assertEquals('sqlite: '.$sqlite, 'sqlite: '.trim($criteria->projections($this->sqconn, $this->peer), ' '));
  }
  
  #[@test]
  function countTest() {
    $this->assertProjection(
      'count(*) as `count`',
      'count(*) as \'count\'',
      'count(*) as "count"',
      'count(*) as \'count\'',
      create(new \rdbms\Criteria())->setProjection(Projections::count())
    );
  }

  #[@test]
  function countColumnTest() {
    $this->assertProjection(
      'count(job_id) as `count_job_id`',
      'count(job_id) as \'count_job_id\'',
      'count(job_id) as "count_job_id"',
      'count(job_id) as \'count_job_id\'',
      create(new \rdbms\Criteria())->setProjection(Projections::count(Job::column('job_id')), 'count_job_id')
    );
  }

  #[@test]
  function countColumnAliasTest() {
    $this->assertProjection(
      'count(job_id) as `counting all`',
      'count(job_id) as \'counting all\'',
      'count(job_id) as "counting all"',
      'count(job_id) as \'counting all\'',
      create(new \rdbms\Criteria())->setProjection(Projections::count(Job::column('job_id')), "counting all")
    );
  }

  #[@test]
  function countAliasTest() {
    $this->assertProjection(
      'count(*) as `counting all`',
      'count(*) as \'counting all\'',
      'count(*) as "counting all"',
      'count(*) as \'counting all\'',
      create(new \rdbms\Criteria())->setProjection(Projections::count('*'), "counting all")
    );
  }

  #[@test]
  function avgTest() {
    $this->assertProjection(
      'avg(job_id)',
      'avg(job_id)',
      'avg(job_id)',
      'avg(job_id)',
      create(new \rdbms\Criteria())->setProjection(Projections::average(Job::column("job_id")))
    );
  }

  #[@test]
  function sumTest() {
    $this->assertProjection(
      'sum(job_id)',
      'sum(job_id)',
      'sum(job_id)',
      'sum(job_id)',
      create(new \rdbms\Criteria())->setProjection(Projections::sum(Job::column("job_id")))
    );
  }

  #[@test]
  function minTest() {
    $this->assertProjection(
      'min(job_id)',
      'min(job_id)',
      'min(job_id)',
      'min(job_id)',
      create(new \rdbms\Criteria())->setProjection(Projections::min(Job::column("job_id")))
    );
  }

  #[@test]
  function maxTest() {
    $this->assertProjection(
      'max(job_id)',
      'max(job_id)',
      'max(job_id)',
      'max(job_id)',
      create(new \rdbms\Criteria())->setProjection(Projections::max(Job::column("job_id")))
    );
  }

  #[@test]
  function propertyTest() {
    $this->assertProjection(
      'job_id',
      'job_id',
      'job_id',
      'job_id',
      create(new \rdbms\Criteria())->setProjection(Projections::property(Job::column("job_id")))
    );
  }

  #[@test]
  function propertyListTest() {
    $this->assertProjection(
      'job_id, title',
      'job_id, title',
      'job_id, title',
      'job_id, title',
      create(new \rdbms\Criteria())->setProjection(Projections::projectionList()
        ->add(Projections::property(Job::column('job_id')))
        ->add(Projections::property(Job::column('title')))
    ));
    $this->assertClass(
      Projections::projectionList()->add(Projections::property(Job::column('job_id'))),
      'rdbms.criterion.ProjectionList'
    );
  }

  #[@test]
  function propertyListAliasTest() {
    $this->assertProjection(
      'job_id as `id`, title',
      'job_id as \'id\', title',
      'job_id as "id", title',
      'job_id as \'id\', title',
      create(new \rdbms\Criteria())->setProjection(Projections::projectionList()
        ->add(Projections::property(Job::column('job_id')), 'id')
        ->add(Job::column('title'))
    ));
  }

  #[@test]
  function setProjectionTest() {
    $crit= new \rdbms\Criteria();
    $this->assertFalse($crit->isProjection());
    $crit->setProjection(Projections::property(Job::column('job_id')));
    $this->assertTrue($crit->isProjection());
    $crit->setProjection(null);
    $this->assertFalse($crit->isProjection());
    $crit->setProjection(Job::column('job_id'));
    $this->assertTrue($crit->isProjection());
    $crit->setProjection();
    $this->assertFalse($crit->isProjection());
  }

  #[@test]
  function withProjectionTest() {
    $crit= new \rdbms\Criteria();
    $this->assertClass(
      $crit->withProjection(Projections::property(Job::column('job_id'))),
      'rdbms.Criteria'
    );
    $this->assertFalse($crit->isProjection());
    $this->assertTrue($crit->withProjection(Projections::property(Job::column('job_id')))->isProjection());
  }
}
