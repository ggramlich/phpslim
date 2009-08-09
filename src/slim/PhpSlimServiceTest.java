package slim;

import fitnesse.slim.SlimServiceTest;
import static slim.TestSuite.getTestIncludePath;

public class PhpSlimServiceTest extends SlimServiceTest {
  protected void startSlimService() throws Exception {
    PhpSlimService.main(new String[]{getTestIncludePath(), "8099"});
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
