package slim;

import util.CommandLine;
import fitnesse.slim.SlimServer;
import fitnesse.slim.SlimService;

public class PhpSlimService extends SlimService {
  private static String includePath;

  public static void main(String[] args) throws Exception {
    if (parseCommandLine(args)) {
      startWithFactory(args, new PhpSlimFactory(includePath));
    } else {
      parseCommandLineFailed(args);
    }
  }

  protected static boolean parseCommandLine(String[] args) {
    CommandLine commandLine = new CommandLine("[-v] includepath port");
    if (commandLine.parse(args)) {
      verbose = commandLine.hasOption("v");
      includePath = commandLine.getArgument("includepath");
      String portString = commandLine.getArgument("port");
      port = Integer.parseInt(portString);
      return true;
    }
    return false;
  }

  public PhpSlimService(int port, SlimServer slimServer) throws Exception {
    super(port, slimServer);
  }
}
