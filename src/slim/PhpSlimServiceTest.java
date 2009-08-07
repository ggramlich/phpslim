package slim;

import fitnesse.slim.SlimServiceTest;

public class PhpSlimServiceTest extends SlimServiceTest {
  protected void startSlimService() throws Exception {
    PhpSlimService.main(new String[]{"8099"});
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
