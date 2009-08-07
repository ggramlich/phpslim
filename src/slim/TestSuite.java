package slim;
import org.junit.runner.RunWith;
import org.junit.runners.Suite;

@RunWith(Suite.class)
@Suite.SuiteClasses(
  {
    PhpBridgeTest.class,
    PhpSlimInstanceCreationTest.class,
    PhpSlimMethodInvocationTest.class,
    PhpListExecutorTest.class
  }
)
public class TestSuite {

}
