package slim;

import static slim.TestSuite.getTestIncludePath;

import org.junit.BeforeClass;

import fitnesse.slim.Jsr223StatementExecutorTestBase;

public class PhpStatementExecutorTest extends Jsr223StatementExecutorTestBase {

  @BeforeClass
  public static void setUpClass() {
    slimFactory = new PhpSlimFactory(getTestIncludePath());
    bridge = slimFactory.getBridge();
  }

  protected String getTestModulePath() {
    return "TestModule.SystemUnderTest";
  }

  @Override
  protected String annotatedFixtureName() {
    return "TestModule_SystemUnderTest_" + super.annotatedFixtureName();
  }

  @Override
  protected String voidMessage() {
    return null;
  }

  @Override
  protected String echoMethodName() {
    return "echoString";
  }

}
