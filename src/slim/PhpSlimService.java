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
    CommandLine commandLine = new CommandLine("[-v] [-i includepath] port");
    if (commandLine.parse(args)) {
      verbose = commandLine.hasOption("v");
      if (commandLine.hasOption("i")) {
          includePath = commandLine.getOptionArgument("i", "includepath");
      } else {
          includePath = System.getProperty("java.class.path");
      }
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
