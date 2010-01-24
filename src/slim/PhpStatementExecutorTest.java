package slim;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertFalse;
import static slim.TestSuite.getTestIncludePath;

import org.junit.AfterClass;
import org.junit.Before;
import org.junit.BeforeClass;

import fitnesse.slim.Jsr223Bridge;
import fitnesse.slim.StatementExecutorTestBase;

public class PhpStatementExecutorTest extends StatementExecutorTestBase {

  public static class FileSupportPhp extends FileSupport {

    private FixtureProxy fixtureProxy;

    public FileSupportPhp(FixtureProxy fixtureProxy) {
      this.fixtureProxy = fixtureProxy;
    }

    @Override
    public void delete(String fileName) {
      fixtureProxy.delete(fileName);
    }

    @Override
    public boolean deleteCalled() {
      return fixtureProxy.deleteCalled();
    }
  }

  public static class EchoSupportPhp extends EchoSupport {

    private FixtureProxy fixtureProxy;

    public EchoSupportPhp(FixtureProxy fixtureProxy) {
      this.fixtureProxy = fixtureProxy;
    }

    @Override
    public void echo() {
      fixtureProxy.echo();
    }

    @Override
    public boolean echoCalled() {
      return fixtureProxy.echoCalled();
    }

    @Override
    public void speak() {
      fixtureProxy.speak();
    }

    @Override
    public boolean speakCalled() {
      return fixtureProxy.speakCalled();
    }
  }

  public static class SimpleFixturePhp extends SimpleFixture {
    private FixtureProxy fixtureProxy;

    public SimpleFixturePhp(FixtureProxy fixtureProxy) {
      this.fixtureProxy = fixtureProxy;
    }

    @Override
    public void echo() {
      fixtureProxy.echo();
    }

    @Override
    public boolean echoCalled() {
      return fixtureProxy.echoCalled();
    }
  }

  public static class FixtureWithNamedSystemUnderTestPhp extends
      FixtureWithNamedSystemUnderTestBase {

    private FixtureProxy fixtureProxy;

    public FixtureWithNamedSystemUnderTestPhp(FixtureProxy fixtureProxy) {
      this.fixtureProxy = fixtureProxy;
    }

    @Override
    public void echo() {
      fixtureProxy.echo();
    }

    @Override
    public boolean echoCalled() {
      return fixtureProxy.echoCalled();
    }

    @Override
    public MySystemUnderTestBase getSystemUnderTest() {
      return fixtureProxy.getSystemUnderTest();
    }
  }

  public static class MySystemUnderTestPhp extends MySystemUnderTestBase {
    private FixtureProxy fixtureProxy;

    public MySystemUnderTestPhp(FixtureProxy fixtureProxy) {
      this.fixtureProxy = fixtureProxy;
    }

    @Override
    public void echo() {
      fixtureProxy.echo();
    }

    @Override
    public boolean echoCalled() {
      return fixtureProxy.echoCalled();
    }

    @Override
    public void speak() {
      fixtureProxy.speak();
    }

    @Override
    public boolean speakCalled() {
      return fixtureProxy.speakCalled();
    }
  }

  public static class MyAnnotatedSystemUnderTestFixturePhp extends
      MyAnnotatedSystemUnderTestFixture {
    private FixtureProxy fixtureProxy;

    public MyAnnotatedSystemUnderTestFixturePhp(FixtureProxy fixtureProxy) {
      this.fixtureProxy = fixtureProxy;
    }

    @Override
    public void echo() {
      fixtureProxy.echo();
    }

    @Override
    public boolean echoCalled() {
      return fixtureProxy.echoCalled();
    }

    @Override
    public MySystemUnderTestBase getSystemUnderTest() {
      return fixtureProxy.getSystemUnderTest();
    }
  }

  public static class FixtureProxy implements Echo, Speak, Delete,
      SystemUnderTestFixture {

    private Object proxy;

    public FixtureProxy(Object instance) {
      proxy = instance;
    }

    @Override
    public void echo() {
    }

    @Override
    public boolean echoCalled() {
      return (Boolean) callMethod("echoCalled");
    }

    @Override
    public void speak() {
    }

    @Override
    public boolean speakCalled() {
      return (Boolean) callMethod("speakCalled");
    }

    @Override
    public void delete(String fileName) {
    }

    @Override
    public boolean deleteCalled() {
      return (Boolean) callMethod("deleteCalled");
    }

    @Override
    public MySystemUnderTestBase getSystemUnderTest() {
      return new MySystemUnderTestPhp(new FixtureProxy(
          callMethod("getSystemUnderTest")));
    }

    private Object callMethod(String method, Object... args) {
      try {
        return bridge.invokeMethod(proxy, method, args);
      } catch (Throwable e) {
        return e.toString();
      }
    }
  }

  private static PhpSlimFactory slimFactory;
  private static Jsr223Bridge bridge;

  @BeforeClass
  public static void setUpClass() {
    slimFactory = new PhpSlimFactory(getTestIncludePath());
    bridge = slimFactory.getBridge();
  }

  @AfterClass
  public static void tearDownClass() {
    slimFactory.stop();
  }

  @Override
  @Before
  public void init() throws Exception {
    statementExecutor = slimFactory.getStatementExecutor();
    statementExecutor.addPath("TestModule.SystemUnderTest");
  }

  @Override
  protected MyAnnotatedSystemUnderTestFixture createAnnotatedFixture() {
    createFixtureInstance(annotatedFixtureName());
    return new MyAnnotatedSystemUnderTestFixturePhp(
        (FixtureProxy) getVerifiedInstance());
  }

  @Override
  protected FixtureWithNamedSystemUnderTestBase createNamedFixture() {
    createFixtureInstance(namedFixtureName());
    return new FixtureWithNamedSystemUnderTestPhp(
        (FixtureProxy) getVerifiedInstance());
  }

  @Override
  protected SimpleFixture createSimpleFixture() {
    createFixtureInstance(simpleFixtureName());
    return new SimpleFixturePhp((FixtureProxy) getVerifiedInstance());
  }

  @Override
  protected EchoSupport createEchoLibrary() {
    String instanceName = "library" + library++;
    Object created = statementExecutor.create(instanceName, echoLibraryName(),
        new Object[] {});
    assertEquals("OK", created);
    return new EchoSupportPhp(new FixtureProxy(statementExecutor
        .getInstance(instanceName)));
  }

  @Override
  protected FileSupport createFileSupportLibrary() {
    String instanceName = "library" + library++;
    Object created = statementExecutor.create(instanceName, fileSupportName(),
        new Object[] {});
    assertEquals("OK", created);
    return new FileSupportPhp(new FixtureProxy(statementExecutor
        .getInstance(instanceName)));
  }

  @Override
  protected Echo getVerifiedInstance() {
    FixtureProxy myInstance = new FixtureProxy(statementExecutor
        .getInstance(INSTANCE_NAME));
    assertFalse(myInstance.echoCalled());
    return myInstance;
  }

  protected void createFixtureInstance(String fixtureClass) {
    Object created = statementExecutor.create(INSTANCE_NAME, fixtureClass,
        new Object[] {});
    assertEquals("OK", created);
  }

  @Override
  protected String annotatedFixtureName() {
    return "TestModule_SystemUnderTest_" + "MyAnnotatedSystemUnderTestFixture";
  }

  @Override
  protected String namedFixtureName() {
    return "FixtureWithNamedSystemUnderTest";
  }

  @Override
  protected String echoLibraryName() {
    return "EchoSupport";
  }

  @Override
  protected String fileSupportName() {
    return "FileSupport";
  }

  @Override
  protected String simpleFixtureName() {
    return "SimpleFixture";
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
