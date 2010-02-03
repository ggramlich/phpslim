package slim;

import fitnesse.slim.Jsr223Bridge;
import fitnesse.slim.Jsr223SlimFactory;
import fitnesse.slim.NameTranslator;
import fitnesse.slim.NameTranslatorIdentity;
import fitnesse.slim.StatementExecutorInterface;

public class PhpSlimFactory extends Jsr223SlimFactory {
  private static Jsr223Bridge phpBridge;
  private String includePath;

  public PhpSlimFactory(String includePath) {
    this.includePath = includePath;
  }

  public synchronized Jsr223Bridge getBridge() {
    // Singleton behavior
    if (null == phpBridge) {
      phpBridge = new PhpBridge(includePath);
    }
    return phpBridge;
  }

  public void stop() {
    closeBridge();
  }

  private synchronized void closeBridge() {
    getBridge().close();
    phpBridge = null;
  }

  public StatementExecutorInterface getStatementExecutor() throws Exception {
    return new PhpStatementExecutor(getBridge());
  }

  @Override
  public NameTranslator getMethodNameTranslator() {
    return new NameTranslatorIdentity();
  }

}
