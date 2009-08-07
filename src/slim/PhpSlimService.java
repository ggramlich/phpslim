package slim;

import fitnesse.slim.SlimServer;
import fitnesse.slim.SlimService;

public class PhpSlimService extends SlimService {
  public static void main(String[] args) throws Exception {
    startWithFactory(args, new PhpSlimFactory());
  }

  public PhpSlimService(int port, SlimServer slimServer) throws Exception {
    super(port, slimServer);
  }
}
