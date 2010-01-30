package slim;

import java.io.File;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;

@RunWith(Suite.class)
@Suite.SuiteClasses( { PhpBridgeTest.class, PhpSlimInstanceCreationTest.class,
    PhpSlimMethodInvocationTest.class, PhpListExecutorTest.class,
    PhpSlimServiceTest.class, PhpStatementExecutorTest.class,
    PhpHashWidgetConversionTest.class })
public class TestSuite {
  public static String getTestIncludePath() {
    return new File("PhpSlim").getAbsolutePath();
  }
}
