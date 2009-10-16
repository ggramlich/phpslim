package slim;

import fitnesse.slim.SlimServiceTestBase;
import static slim.TestSuite.getTestIncludePath;

public class PhpSlimServiceTest extends SlimServiceTestBase {
  protected void startSlimService() throws Exception {
    PhpSlimService.main(new String[]{"-i", getTestIncludePath(), "8099"});
  }

  protected String getImport() {
    return "TestModule";
  }

  protected String expectedExceptionMessage() {
    return "exception 'Exception' with message 'normal exception'";
  }
  
  protected String expectedStopTestExceptionMessage() {
    return "ABORT_SLIM_TEST:message:<<test stopped in TestSlim>>";
  }
}
